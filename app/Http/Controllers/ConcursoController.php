<?php

namespace App\Http\Controllers;

use App\Models\Modalidades;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Concursos;
use App\Models\Estados;
use App\Models\User;
use App\Models\Criterio;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Equipo;
Use App\Models\Asesor;
use App\Models\Evaluaciones;
use App\Models\PuntajeEvaluacion;
use App\Models\CriteriosEvaluacion;
use App\Models\Linea;
use App\Models\Evaluacion;
use App\Models\ResultadosFinales;
use App\Models\PuntajesEvaluacion;
use App\Models\PuntajesParciales;

class ConcursoController extends Controller
{
    /**
     * Muestra la página de concursos
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        
        // Cargar concursos con relaciones optimizadas
        $concursos = Concursos::with([
            'plantel',
            'estadoRelation' => function($query) {
                $query->select('idestado', 'nombre');
            }
        ])->get();

        // Procesar los datos para incluir nombres de estado, plantel y si el evaluador está inscrito
        $concursos->each(function ($concurso) use ($user) {
            // Para concursos estatales
            if ($concurso->fase === 'estatal') {
                $concurso->estado_nombre = $concurso->estadoRelation->nombre ?? 'No especificado';
            }
            
            // Para concursos locales (opcional)
            if ($concurso->fase === 'local' && $concurso->plantel) {
                $concurso->plantel_nombre = $concurso->plantel->nombre_corto ?? null;
            }

            // Verificar si el evaluador está inscrito
            if ($user->rol === 'evaluador') {
                $concurso->inscrito = DB::table('concurso_evaluador')
                    ->where('concurso_id', $concurso->id)
                    ->where('evaluador_id', $user->id)
                    ->exists();
            }
        });

        // Filtrar para líderes si es necesario
        if ($user->rol === 'lider') {
            if ($user->concurso_registrado_id) {
                $concursos = $concursos->where('id', $user->concurso_registrado_id);
            } else {
                $concursos = $concursos->filter(function ($concurso) use ($user) {
                    return $concurso->plantel && $concurso->plantel->estado_id === $user->estado_id;
                });
            }
        }

        // Asegurar que concursos sea un array indexado para Vue
        $concursos = $concursos->values()->all();

        return Inertia::render('ConcursosLayouts/Concursos', [
            'concursos' => $concursos,
            'inscrito' => (bool)$user->concurso_registrado_id,
            'flash' => ['success' => request()->query('success', '')]
        ]);
    }

    /**
     * Almacena un nuevo concurso
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_terminacion' => 'required|date|after_or_equal:fecha_inicio',
            'fase' => 'required|string|in:local,estatal,nacional',
            'estado' => 'required_if:fase,estatal|nullable|integer|exists:estados,idestado',
            'plantel_id' => 'required_if:fase,local|nullable|integer|exists:planteles,id_plantel',
        ]);

        // Crear el concurso con los datos validados
        $concurso = Concursos::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_terminacion' => $validated['fecha_terminacion'],
            'fase' => $validated['fase'],
            'estado' => $validated['estado'] ?? null,
            'plantel_id' => $validated['plantel_id'] ?? null,
            'status' => 'abierto',
            'fecha_apertura' => now()->toDateString(),
        ]);

        return redirect()->route('concursos.index')->with('success', 'Concurso creado exitosamente.');
    }

    /**
     * Muestra el formulario de edición
     *
     * @param  \App\Models\Concursos  $concurso
     * @return \Inertia\Response
     */
    public function edit(Concursos $concurso)
    {
        return Inertia::render('ConcursosLayouts/EditarConcurso', [
            'concurso' => $concurso->load(['estadoRelation', 'plantel'])
        ]);
    }

    /**
     * Actualiza un concurso existente
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_terminacion' => 'required|date|after_or_equal:fecha_inicio',
            'fase' => 'required|string|in:local,estatal,nacional',
            'estado' => 'required_if:fase,estatal|nullable|integer|exists:estados,idestado',
            'plantel_id' => 'required_if:fase,local|nullable|integer|exists:planteles,id_plantel',
        ]);

        $concurso = Concursos::findOrFail($id);
        $concurso->update($validated);

        return redirect()->route('concursos.index')->with('success', 'Concurso actualizado exitosamente.');
    }

    /**
     * Elimina un concurso
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $concurso = Concursos::findOrFail($id);
        $concurso->delete();

        return redirect()->route('concursos.index')->with('success', 'Concurso eliminado exitosamente.');
    }

    /**
     * Inscribe al usuario en un concurso
     *
     * @param  int  $concursoId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inscribirse($concursoId)
    {
        $user = auth()->user();

        if ($user->concurso_registrado_id) {
            return redirect()->route('gestion-de-proyectos')->with('error', 'Ya estás inscrito en un concurso.');
        }

        $concurso = Concursos::find($concursoId);
        if (!$concurso) {
            return redirect()->route('concursos.index')->with('error', 'El concurso no existe.');
        }

        $user->concurso_registrado_id = $concursoId;
        $user->save();

        return redirect()->route('gestion-de-proyectos')->with('success', 'Inscripción exitosa.');
    }

    /**
     * Método de diagnóstico para verificar estados (temporal)
     */
    public function diagnosticarEstados()
    {
        $concursosProblema = Concursos::where('fase', 'estatal')
            ->whereNotNull('estado')
            ->whereDoesntHave('estadoRelation')
            ->get();

        if ($concursosProblema->isNotEmpty()) {
            $resultado = "Los siguientes concursos tienen IDs de estado inválidos:\n";
            foreach ($concursosProblema as $concurso) {
                $resultado .= "Concurso ID: {$concurso->id}, Estado ID: {$concurso->estado}\n";
            }
            return response($resultado);
        }

        return response("Todos los concursos estatales tienen estados válidos.");
    }

    /**
     * Cambia el estado de un concurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $concursoId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambiarEstado(Request $request, $id)
    {
        $concurso = Concursos::findOrFail($id);
        $nuevoEstado = $request->input('nuevo_estado');

        if ($nuevoEstado === 'cerrado') {
            $concurso->status = 'cerrado';
            $concurso->save();

            // Asignar equipos a asesores
            $this->asignarEvaluaciones($concurso);

            return back()->with('success', 'Concurso cerrado y evaluaciones asignadas.');
        } elseif ($nuevoEstado === 'abierto') {
            // Redirigir a la nueva función para abrir el concurso
            return $this->abrirConcurso($request, $id);
        }

        return back()->with('error', 'Estado no reconocido.');
    }

    /**
     * Cambia el estado de un concurso a abierto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function abrirConcurso(Request $request, $id)
    {
        $concurso = Concursos::findOrFail($id);

        if ($concurso->status === 'abierto') {
            return back()->with('info', 'El concurso ya está abierto.');
        }

        $concurso->status = 'abierto';
        $concurso->save();

        return back()->with('success', 'El concurso ha sido reabierto correctamente.');
    }

    private function asignarEvaluaciones($concurso)
    {
        \Log::info("Iniciando asignación de evaluaciones para concurso ID: {$concurso->id}");

        // Obtener los equipos del concurso
        $equipos = Equipo::with('proyecto')->where('concurso_id', $concurso->id)->get();
        \Log::info("Equipos encontrados: " . $equipos->count());

        // Obtener los evaluadores inscritos en el concurso, junto con su perfil profesional
        $evaluadores = \DB::table('concurso_evaluador')
            ->join('users', 'concurso_evaluador.evaluador_id', '=', 'users.id')
            ->join('evaluadores', 'users.id', '=', 'evaluadores.userID')
            ->where('concurso_evaluador.concurso_id', $concurso->id)
            ->select('users.id as user_id', 'users.name', 'evaluadores.perfil')
            ->get();

        \Log::info("Evaluadores encontrados: " . $evaluadores->count(), ['evaluadores' => $evaluadores->pluck('user_id')->toArray()]);

        if ($evaluadores->isEmpty()) {
            $ids = \DB::table('concurso_evaluador')
                ->where('concurso_id', $concurso->id)
                ->pluck('evaluador_id');
            \Log::warning("No se encontraron evaluadores con join. Ids en concurso_evaluador: " . json_encode($ids));
        }

        if ($equipos->isEmpty() || $evaluadores->isEmpty()) {
            \Log::warning("No hay equipos o evaluadores para asignar en el concurso ID: {$concurso->id}");
            return;
        }

        // Convertir los perfiles de evaluadores a arrays
        $evaluadores = $evaluadores->map(function ($evaluador) {
            $evaluador->perfil = json_decode($evaluador->perfil, true);
            return $evaluador;
        });

        foreach ($equipos as $equipo) {
            \Log::info("Procesando equipo ID: {$equipo->id}");
            $perfilesSolicitados = $equipo->proyecto->perfil_jurado ?? [];
            if (!is_array($perfilesSolicitados)) {
                $perfilesSolicitados = json_decode($perfilesSolicitados, true) ?: [];
            }
            \Log::info("Perfiles solicitados para equipo {$equipo->id}: " . json_encode($perfilesSolicitados));

            // Filtrar evaluadores compatibles
            $evaluadoresCompatibles = $evaluadores->filter(function ($evaluador) use ($perfilesSolicitados) {
                if (!is_array($evaluador->perfil)) return false;
                foreach ($perfilesSolicitados as $perfilSolicitado) {
                    foreach ($evaluador->perfil as $perfilEvaluador) {
                        if (stripos($perfilEvaluador, $perfilSolicitado) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            });

            // Seleccionar entre 2 y 4 evaluadores (priorizando compatibles)
            $asignados = collect();
            $numEvaluadores = min(4, max(2, $evaluadores->count()));
            $compatibles = $evaluadoresCompatibles->shuffle()->take($numEvaluadores);
            $asignados = $compatibles;
            if ($asignados->count() < 2) {
                // Si hay menos de 2 compatibles, completar con otros evaluadores no asignados
                $faltan = 2 - $asignados->count();
                $restantes = $evaluadores->whereNotIn('user_id', $asignados->pluck('user_id'))->shuffle()->take($faltan);
                $asignados = $asignados->concat($restantes);
            } else if ($asignados->count() < $numEvaluadores) {
                // Si hay menos de 4 compatibles, completar hasta 4 con otros
                $faltan = $numEvaluadores - $asignados->count();
                $restantes = $evaluadores->whereNotIn('user_id', $asignados->pluck('user_id'))->shuffle()->take($faltan);
                $asignados = $asignados->concat($restantes);
            }
            // Evitar duplicados
            $asignados = $asignados->unique('user_id');
            // Limitar a máximo 4
            $asignados = $asignados->take(4);

            foreach ($asignados as $evaluador) {
                try {
                    Evaluaciones::create([
                        'evaluador_id' => $evaluador->user_id,
                        'equipo_id' => $equipo->id,
                        'estado' => 'pendiente',
                    ]);
                    \Log::info("Evaluación creada para equipo ID: {$equipo->id} y evaluador ID: {$evaluador->user_id}");
                } catch (\Exception $e) {
                    \Log::error("Error al crear evaluación para equipo ID: {$equipo->id} y evaluador ID: {$evaluador->user_id}: " . $e->getMessage());
                }
            }
        }

        \Log::info("Finalizó la asignación de evaluaciones para concurso ID: {$concurso->id}");
    }

    
    // app/Http/Controllers/ConcursoController.php
    public function registroCriterios()
    {
        $concursos = Concursos::with(['criteriosEvaluacion', 'criteriosEvaluacion.modalidad'])->get();
        $modalidades = Modalidades::all();
        
        return Inertia::render('ConcursosLayouts/RegistroCriterios', [
            'concursos' => $concursos,
            'modalidades' => $modalidades,
            'criteriosExistentes' => CriteriosEvaluacion::with('modalidad')->get()->toArray()
        ]);
    }
    
    public function storeTipo(Request $request)
    {
        $validated = $request->validate([
            'concurso_id' => 'required|exists:concursos,id',
            'modalidad_id' => 'required|exists:modalidades,id',
            'tipo_criterio' => 'required|in:informe,modalidad,exposicion',
            'criterios' => 'required|array|min:1',
            'criterios.*.nombre' => 'required|string|max:255',
            'criterios.*.puntaje_maximo' => 'required|numeric|min:1|max:100',
            'puntos_asignados' => 'required|array',
            'puntos_asignados.informe' => 'required|numeric|min:0|max:100',
            'puntos_asignados.modalidad' => 'required|numeric|min:0|max:100',
            'puntos_asignados.exposicion' => 'required|numeric|min:0|max:100',
        ]);
    
        // Validar que la suma de puntos asignados sea 100
        $sumaPuntosAsignados = array_sum($validated['puntos_asignados']);
        if ($sumaPuntosAsignados != 100) {
            return back()->withErrors([
                'puntos_asignados' => "La suma de puntos asignados debe ser 100 (actual: {$sumaPuntosAsignados})"
            ]);
        }
    
        // Validar que los criterios sumen lo asignado para este tipo
        $sumaCriterios = array_sum(array_column($validated['criterios'], 'puntaje_maximo'));
        $puntosEsperados = $validated['puntos_asignados'][$validated['tipo_criterio']];
        
        if ($sumaCriterios != $puntosEsperados) {
            return back()->withErrors([
                'criterios' => "Los criterios deben sumar exactamente {$puntosEsperados} puntos para este tipo"
            ]);
        }
    
        DB::transaction(function () use ($validated) {
            // Eliminar criterios existentes de este tipo
            CriteriosEvaluacion::where('concurso_id', $validated['concurso_id'])
                ->where('modalidad_id', $validated['modalidad_id'])
                ->where('tipo_criterio', $validated['tipo_criterio'])
                ->delete();
    
            // Crear nuevos criterios
            foreach ($validated['criterios'] as $criterioData) {
                CriteriosEvaluacion::create([
                    'concurso_id' => $validated['concurso_id'],
                    'modalidad_id' => $validated['modalidad_id'],
                    'tipo_criterio' => $validated['tipo_criterio'],
                    'nombre' => $criterioData['nombre'],
                    'puntaje_maximo' => $criterioData['puntaje_maximo'],
                ]);
            }
        });
    
        return redirect()->back()->with('success', 'Criterios guardados exitosamente.');
    }
    
    public function storeModalidad(Request $request)
    {
        $validated = $request->validate([
            'concurso_id' => 'required|exists:concursos,id',
            'modalidad_id' => 'required|exists:modalidades,id',
            'criterios' => 'required|array|min:3',
            'puntos_asignados' => 'required|array',
            'puntos_asignados.informe' => 'required|numeric|min:0|max:100',
            'puntos_asignados.modalidad' => 'required|numeric|min:0|max:100',
            'puntos_asignados.exposicion' => 'required|numeric|min:0|max:100',
        ]);
    
        // Validación adicional de suma total
        $sumaPuntos = array_sum($validated['puntos_asignados']);
        if ($sumaPuntos != 100) {
            return response()->json([
                'success' => false,
                'message' => "La suma total de puntos debe ser 100 (actual: $sumaPuntos)"
            ], 422);
        }
    
        DB::beginTransaction();
        try {
            // Eliminar criterios existentes
            CriteriosEvaluacion::where('concurso_id', $validated['concurso_id'])
                ->where('modalidad_id', $validated['modalidad_id'])
                ->delete();
    
            // Insertar nuevos criterios
            foreach ($validated['criterios'] as $criterio) {
                CriteriosEvaluacion::create([
                    'concurso_id' => $validated['concurso_id'],
                    'modalidad_id' => $validated['modalidad_id'],
                    'tipo_criterio' => $criterio['tipo_criterio'],
                    'nombre' => $criterio['nombre'],
                    'puntaje_maximo' => $criterio['puntaje_maximo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Criterios guardados correctamente'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registrarEvaluador(Request $request, $concursoId)
    {
        $user = auth()->user();

        // Validate that the user is an evaluator
        if ($user->rol !== 'evaluador') {
            return redirect()->route('concursos.index')->with('error', 'Solo los evaluadores pueden inscribirse.');
        }

        // Validate that the contest exists
        $concurso = Concursos::find($concursoId);
        if (!$concurso) {
            return redirect()->route('concursos.index')->with('error', 'El concurso no existe.');
        }

        // Check if the evaluator is already registered for this contest
        $alreadyRegistered = DB::table('concurso_evaluador')
            ->where('concurso_id', $concursoId)
            ->where('evaluador_id', $user->id)
            ->exists();

        if ($alreadyRegistered) {
            return redirect()->route('concursos.index')->with('error', 'Ya estás inscrito como evaluador en este concurso.');
        }

        // Register the evaluator for the contest
        DB::table('concurso_evaluador')->insert([
            'concurso_id' => $concursoId,
            'evaluador_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('concursos.index')->with('success', 'Te has inscrito como evaluador exitosamente.');
    }

    public function verEquipos()
    {
        // Retrieve all teams with their related projects, contests, participants, and advisors
        $equipos = Equipo::with([
            'proyecto.concurso', 
            'participantes',
            'asesores'
        ])->get();
    
        return Inertia::render('ConcursosLayouts/EquiposRegistrados', [
            'equipos' => $equipos,
        ]);
    }

    public function getEvaluaciones($concursoId)
    {
        // Fetch evaluations through the Equipo model and include related data
        $evaluaciones = Evaluaciones::whereHas('equipo', function ($query) use ($concursoId) {
            $query->where('concurso_id', $concursoId);
        })->with(['evaluador', 'equipo.proyecto'])->get();

        return response()->json([
            'success' => true,
            'evaluaciones' => $evaluaciones,
        ]);
    }

    /**
     * Finaliza un concurso si todas las evaluaciones están completadas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $concursoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizarConcurso(Request $request, $concursoId)
    {
        $concurso = Concursos::find($concursoId);

        if (!$concurso) {
            return response()->json([
                'success' => false,
                'message' => 'El concurso no existe.',
            ], 404);
        }

        $evaluacionesPendientes = Evaluaciones::whereHas('equipo', function ($query) use ($concursoId) {
            $query->where('concurso_id', $concursoId);
        })->where('estado', 'pendiente')->count();

        if ($evaluacionesPendientes > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede finalizar el concurso. Aún hay evaluaciones pendientes.',
            ], 400);
        }

        // Calcular promedios finales
        $equipos = Equipo::where('concurso_id', $concursoId)->get();
        foreach ($equipos as $equipo) {
            // Obtener todas las evaluaciones del equipo
            $evaluaciones = Evaluaciones::where('equipo_id', $equipo->id)->get();
            $puntajesParciales = [];

            foreach ($evaluaciones as $evaluacion) {
                // Sumar los puntajes de todos los criterios de esta evaluación
                $puntajeTotal = PuntajesEvaluacion::where('evaluacion_id', $evaluacion->id)->sum('puntaje_obtenido');
                
                // Loguear el puntaje parcial
                \Log::info("Equipo {$equipo->id} - Evaluacion {$evaluacion->id} - Puntaje parcial: {$puntajeTotal}");

                // Validar que el puntaje parcial no exceda 100
                if ($puntajeTotal > 100) {
                    \Log::warning("Puntaje parcial excede 100: Equipo {$equipo->id}, Evaluacion {$evaluacion->id}, Puntaje: {$puntajeTotal}");
                    $puntajeTotal = 100;
                }

                // Guardar el puntaje parcial (debe ser 100 máx)
                PuntajesParciales::updateOrCreate(
                    [
                        'evaluacion_id' => $evaluacion->id,
                        'equipo_id' => $equipo->id,
                        'concurso_id' => $concursoId,
                    ],
                    [
                        'puntaje_total' => $puntajeTotal,
                    ]
                );
                $puntajesParciales[] = $puntajeTotal;
            }

            if (count($puntajesParciales) > 0) {
                $promedioFinal = array_sum($puntajesParciales) / count($puntajesParciales);

                // Loguear el promedio final
                \Log::info("Equipo {$equipo->id} - Promedio final: {$promedioFinal}");

                // Guardar el resultado final
                ResultadosFinales::updateOrCreate(
                    ['equipo_id' => $equipo->id, 'concurso_id' => $concursoId],
                    ['promedio_final' => $promedioFinal]
                );
            }
        }

        $concurso->status = 'finalizado';
        $concurso->save();

        return response()->json([
            'success' => true,
            'message' => 'El concurso ha sido finalizado exitosamente y los resultados han sido calculados.',
        ]);
    }

    /**
     * Obtiene el podio de un concurso.
     *
     * @param  int  $concursoId
     * @return \Inertia\Response
     */
    public function obtenerPodio($concursoId)
    {
        // Obtener todos los resultados con estado del proyecto
        $resultados = ResultadosFinales::where('concurso_id', $concursoId)
            ->with(['equipo.proyecto']) // Eager-load the proyecto relationship
            ->orderByDesc('promedio_final')
            ->get();

        // Filtrar los que no están descalificados para el podio
        $podio = $resultados->filter(function ($resultado) {
            // Si no hay proyecto, lo excluimos del podio
            if (!$resultado->equipo || !$resultado->equipo->proyecto) return false;
            // Si el estado es 'descalificado', lo excluimos del podio
            return strtolower($resultado->equipo->proyecto->estado ?? '') !== 'descalificado';
        })->take(3)->values();

        // Adjuntar el estado del proyecto a cada resultado para la tabla
        $resultados = $resultados->map(function ($resultado) {
            $resultado->estado_proyecto = $resultado->equipo && $resultado->equipo->proyecto
                ? ($resultado->equipo->proyecto->estado ?? 'En orden')
                : 'N/A';
            return $resultado;
        });

        return Inertia::render('ConcursosLayouts/Podio', [
            'podio' => $podio,
            'resultados' => $resultados,
        ]);
    }

    public function obtenerResumenEvaluaciones($id)
    {
        $concurso = Concursos::findOrFail($id);

        // Usar la relación evaluaciones para contar los estados
        $resumen = [
            'pendientes' => $concurso->evaluaciones()->where('estado', 'pendiente')->count(),
            'completadas' => $concurso->evaluaciones()->where('estado', 'completada')->count(),
        ];

        return response()->json([
            'success' => true,
            'resumen' => $resumen,
        ]);
    }

    /**
     * Obtiene los datos de un concurso por ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConcurso($id)
    {
        $concurso = Concursos::find($id);

        if (!$concurso) {
            return response()->json([
                'success' => false,
                'message' => 'El concurso no existe.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'concurso' => $concurso,
        ]);
    }

    /**
     * Renderiza la vista de resultados de concursos.
     *
     * @return \Inertia\Response
     */
    public function resultados()
    {
        $user = auth()->user();
        $concursoId = $user->concurso_registrado_id;
        $equipoId = $user->equipo_id;

        return Inertia::render('ConcursosLayouts/Resultados', [
            'user' => $user,
            'concursoId' => $concursoId,
            'equipoId' => $equipoId,
        ]);
    }

    /**
     * Genera el reporte de resultados para un equipo en un concurso.
     *
     * @param  int  $concursoId
     * @param  int  $equipoId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generarReporte($concursoId, $equipoId)
    {
        $concurso = Concursos::findOrFail($concursoId);
        $equipo = Equipo::with(['proyecto', 'participantes'])->findOrFail($equipoId);

        // Trae evaluaciones con evaluador y puntajes (y criterio de cada puntaje)
        $evaluaciones = Evaluaciones::with([
            'evaluador',
            'puntajes.criterio'
        ])
        ->where('equipo_id', $equipoId)
        ->get();

        // Obtener perfiles de evaluadores y agregarlos a cada evaluación
        foreach ($evaluaciones as $evaluacion) {
            if ($evaluacion->evaluador) {
                $perfil = \App\Models\Evaluadores::where('userID', $evaluacion->evaluador->id)->value('perfil');
                // Decodificar perfil si es JSON
                $evaluacion->perfil = is_string($perfil) ? json_decode($perfil, true) : $perfil;
            } else {
                $evaluacion->perfil = [];
            }
        }

        // Renderizar la vista PDF
        $pdf = Pdf::loadView('pdf.resultados', [
            'concurso' => $concurso,
            'equipo' => $equipo,
            'evaluaciones' => $evaluaciones, // Cada evaluación tendrá sus puntajes, criterios y perfil
        ]);

        return $pdf->stream('reporte_concurso.pdf');
    }

}