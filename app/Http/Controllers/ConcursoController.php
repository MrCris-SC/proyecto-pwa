<?php

namespace App\Http\Controllers;

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
use App\Models\CriterioEvaluacion;
use App\Models\Evaluaciones;
use App\Models\PuntajeEvaluacion;

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
        }

        return back()->with('error', 'Estado no reconocido.');
    }

    private function asignarEvaluaciones($concurso)
    {
        // Obtener los equipos del concurso
        $equipos = Equipo::where('concurso_id', $concurso->id)->get();

        // Obtener los evaluadores inscritos en el concurso
        $evaluadores = DB::table('concurso_evaluador')
            ->where('concurso_id', $concurso->id)
            ->pluck('evaluador_id');

        if ($equipos->isEmpty() || $evaluadores->isEmpty()) {
            return; // No hay equipos o evaluadores para asignar
        }

        // Convertir la colección de evaluadores a un array para evitar problemas
        $evaluadores = $evaluadores->toArray();

        // Distribuir los equipos entre los evaluadores de manera equitativa
        $evaluadoresCount = count($evaluadores);
        $index = 0;

        foreach ($equipos as $equipo) {
            // Asegurarse de que el índice no exceda el número de evaluadores
            $evaluadorId = $evaluadores[$index % $evaluadoresCount];

            // Crear la evaluación para el equipo y evaluador
            Evaluaciones::create([
                'evaluador_id' => $evaluadorId,
                'equipo_id' => $equipo->id,
                'estado' => 'pendiente',
            ]);

            $index++;
        }
    }

    
    // app/Http/Controllers/ConcursoController.php

    public function registroCriterios()
    {
        $concursos = Concursos::with('criterios')->get();
        
        return Inertia::render('ConcursosLayouts/RegistroCriterios', [
            'concursos' => $concursos,
        ]);
    }

    public function guardarCriterios(Request $request)
    {
        $validated = $request->validate([
            'concurso_id' => 'required|exists:concursos,id',
            'criterios' => 'required|array|min:1',
            'criterios.*.nombre' => 'required|string|max:255|distinct',
            'criterios.*.puntaje_maximo' => 'required|numeric|min:1|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            // Eliminar criterios existentes para este concurso
            Criterio::where('concurso_id', $validated['concurso_id'])->delete();
            
            // Crear nuevos criterios
            foreach ($validated['criterios'] as $criterioData) {
                Criterio::create([
                    'concurso_id' => $validated['concurso_id'],
                    'nombre' => $criterioData['nombre'],
                    'puntaje_maximo' => $criterioData['puntaje_maximo']
                ]);
            }
        });

        return redirect()->back()->with('success', 'Criterios guardados exitosamente.');
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
    // Se cargan los equipos junto con sus relaciones:
    // - 'proyecto.concurso': carga el proyecto de cada equipo y, a su vez, el concurso asociado al proyecto.
    // - 'participantes': carga los participantes del equipo.
    // - (Opcionalmente) 'concurso': si el equipo tiene directamente una relación con Concurso, se puede cargar también.
    $equipos = Equipo::with([
        'proyecto.concurso', 
        'participantes',
        'concurso' // Si tienes la relación directa en el modelo Equipo.
    ])->get();

    return Inertia::render('ConcursosLayouts/EquiposRegistrados', [
        'equipos' => $equipos,
    ]);
}
}