<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Evaluaciones;
use App\Models\Equipo;
use App\Models\CriteriosEvaluacion;
use App\Models\PuntajesEvaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvaluadorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener todas las evaluaciones (pendientes y completadas) con sus relaciones
        $evaluaciones = Evaluaciones::with([
            'equipo.proyecto.modalidad',
            'equipo.concurso.criteriosEvaluacion' => function($query) {
                $query->orderBy('tipo_criterio')->orderBy('id');
            },
            'puntajes'
        ])
        ->where('evaluador_id', $user->id)
        ->get()
        ->map(function ($evaluacion) {
            return $this->mapearDatosEvaluacion($evaluacion);
        });

        return Inertia::render('ConcursosLayouts/Evaluacion', [
            'evaluacionesPendientes' => $evaluaciones,
            'tiposCriterio' => [
                ['value' => 'informe', 'label' => 'I. Informe'],
                ['value' => 'modalidad', 'label' => 'II. Modalidad'],
                ['value' => 'exposicion', 'label' => 'III. Exposición']
            ]
        ]);
    }

    public function guardarEvaluacion(Request $request, $evaluacionId)
    {
        $validated = $request->validate([
            'puntajes' => 'required|array',
            'puntajes.*.criterio_id' => 'required|exists:criterios_evaluacion,id',
            'puntajes.*.puntaje' => 'required|numeric|min:0',
            'comentarios' => 'nullable|string',
            'tipo_criterio' => 'nullable|string|in:informe,modalidad,exposicion'
        ]);
        
        $evaluacion = Evaluaciones::with(['equipo.proyecto', 'puntajes'])->findOrFail($evaluacionId);
        
        DB::transaction(function () use ($evaluacion, $validated) {
            // Filtrar criterios por tipo si se especificó
            $criteriosAGuardar = $this->filtrarCriteriosPorTipo(
                $validated['puntajes'],
                $evaluacion,
                $validated['tipo_criterio'] ?? null
            );
            
            // Guardar cada puntaje
            foreach ($criteriosAGuardar as $puntajeData) {
                $this->guardarPuntajeIndividual(
                    $evaluacion,
                    $puntajeData['criterio_id'],
                    $puntajeData['puntaje'],
                    $validated['comentarios']
                );
            }
            
            // Marcar como completada si corresponde
            $this->verificarCompletitudEvaluacion($evaluacion, isset($validated['tipo_criterio']));
        });
        
        // Devolver datos actualizados
        return $this->respuestaEvaluacionActualizada($evaluacion, $validated['tipo_criterio'] ?? null);
    }

    /**
     * Métodos auxiliares
     */
    private function mapearDatosEvaluacion($evaluacion)
    {
        $criteriosCompletos = $this->verificarCriteriosCompletos($evaluacion);
        
        return [
            'id' => $evaluacion->id,
            'nombre_proyecto' => $evaluacion->equipo->proyecto->nombre ?? 'Proyecto sin nombre',
            'modalidad_id' => $evaluacion->equipo->proyecto->modalidad_id,
            'modalidad_nombre' => $evaluacion->equipo->proyecto->modalidad->nombre ?? 'Modalidad no especificada',
            'linea_investigacion' => $this->getNombreLineaInvestigacion($evaluacion->equipo->proyecto->linea_investigacion_id),
            'ya_evaluado' => $evaluacion->estado === 'completada' || $criteriosCompletos,
            'evaluacion_completa' => $evaluacion->estado === 'completada',
            'comentarios' => $evaluacion->puntajes->first()->comentario ?? '',
            'criterios' => $evaluacion->equipo->concurso->criteriosEvaluacion
                ->where('modalidad_id', $evaluacion->equipo->proyecto->modalidad_id)
                ->map(function ($criterio) use ($evaluacion) {
                    $puntaje = $evaluacion->puntajes->where('criterio_id', $criterio->id)->first();
                    
                    return [
                        'id' => $criterio->id,
                        'nombre' => $criterio->nombre,
                        'puntaje_maximo' => (float) $criterio->puntaje_maximo,
                        'puntaje_asignado' => $puntaje ? (float) $puntaje->puntaje_obtenido : null,
                        'descripcion' => $criterio->descripcion,
                        'tipo_criterio' => $criterio->tipo_criterio
                    ];
                })
                ->values()
                ->toArray()
        ];
    }

    private function filtrarCriteriosPorTipo($puntajes, $evaluacion, $tipoCriterio = null)
    {
        if (!$tipoCriterio) {
            return $puntajes;
        }
        
        $criteriosDelTipo = CriteriosEvaluacion::where('concurso_id', $evaluacion->equipo->concurso_id)
            ->where('modalidad_id', $evaluacion->equipo->proyecto->modalidad_id)
            ->where('tipo_criterio', $tipoCriterio)
            ->pluck('id');
            
        return array_filter($puntajes, function($p) use ($criteriosDelTipo) {
            return $criteriosDelTipo->contains($p['criterio_id']);
        });
    }

    private function guardarPuntajeIndividual($evaluacion, $criterioId, $puntaje, $comentario)
    {
        PuntajesEvaluacion::updateOrCreate(
            [
                'evaluacion_id' => $evaluacion->id,
                'criterio_id' => $criterioId
            ],
            [
                'puntaje_obtenido' => $puntaje
            ]
        );

        // Guardar comentario en la tabla de evaluaciones (si se proporciona)
        if (!is_null($comentario)) {
            $evaluacion->comentarios = $comentario;
            $evaluacion->save();
        }
    }

    private function verificarCompletitudEvaluacion($evaluacion, $esGuardadoParcial = false)
    {
        if (!$esGuardadoParcial && $this->verificarTodosCriteriosCompletos($evaluacion)) {
            $evaluacion->estado = 'completada';
            $evaluacion->save();
        }
    }

    private function respuestaEvaluacionActualizada($evaluacion, $tipoCriterio = null)
    {
        $evaluacion->refresh();
        $evaluacion->load(['equipo.proyecto.modalidad', 'equipo.concurso.criteriosEvaluacion', 'puntajes']);
        
        return response()->json([
            'success' => true,
            'evaluacion' => $this->mapearDatosEvaluacion($evaluacion),
            'message' => $tipoCriterio 
                ? 'Sección ' . $this->getNombreSeccion($tipoCriterio) . ' guardada correctamente' 
                : 'Evaluación final enviada exitosamente'
        ]);
    }

    private function getNombreSeccion($tipoCriterio)
    {
        $secciones = [
            'informe' => 'I. Informe',
            'modalidad' => 'II. Modalidad',
            'exposicion' => 'III. Exposición'
        ];
        
        return $secciones[$tipoCriterio] ?? '';
    }

    private function verificarCriteriosCompletos($evaluacion)
    {
        $criteriosPorTipo = $evaluacion->equipo->concurso->criteriosEvaluacion
            ->where('modalidad_id', $evaluacion->equipo->proyecto->modalidad_id)
            ->groupBy('tipo_criterio');
            
        foreach ($criteriosPorTipo as $tipo => $criterios) {
            foreach ($criterios as $criterio) {
                if (!$evaluacion->puntajes->where('criterio_id', $criterio->id)->first()) {
                    return false;
                }
            }
        }
        
        return true;
    }

    private function verificarTodosCriteriosCompletos($evaluacion)
    {
        $criterios = $evaluacion->equipo->concurso->criteriosEvaluacion
            ->where('modalidad_id', $evaluacion->equipo->proyecto->modalidad_id);
            
        foreach ($criterios as $criterio) {
            if (!$evaluacion->puntajes->where('criterio_id', $criterio->id)->first()) {
                return false;
            }
        }
        
        return true;
    }

    private function getNombreLineaInvestigacion($id)
    {
        $lineas = [
            1 => 'Desarrollo tecnológico',
            2 => 'Investigación educativa',
            3 => 'Desarrollo sustentable y medio ambiente',
            4 => 'Investigación en ciencias de la salud',
            5 => 'Desarrollo humano, social y emocional'
        ];
        
        return $lineas[$id] ?? 'Desconocida';
    }

    //proyectosAsignados

    public function proyectosAsignados()
    {
        $user = Auth::user();

        $evaluaciones = Evaluaciones::with([
            'equipo.proyecto.modalidad',
            'equipo.concurso'
        ])
        ->where('evaluador_id', $user->id)
        ->get()
        ->map(function ($evaluacion) {
            $equipoId = $evaluacion->equipo->id;
            // Usar el disco 'public' y buscar archivos en la carpeta del equipo
            $archivos = Storage::disk('public')->files("equipos/{$equipoId}");
            $existeCarpeta = count($archivos) > 0;
            return [
                'id' => $evaluacion->id,
                'nombre_proyecto' => $evaluacion->equipo->proyecto->nombre ?? 'Proyecto sin nombre',
                'modalidad' => $evaluacion->equipo->proyecto->modalidad->nombre ?? 'Sin modalidad',
                'concurso' => $evaluacion->equipo->concurso->nombre,
                'estado' => $evaluacion->estado,
                'fecha_asignacion' => $evaluacion->created_at->format('d/m/Y'),
                'equipo_id' => $equipoId,
                'documentos_disponibles' => $existeCarpeta
            ];
        });

        return Inertia::render('ConcursosLayouts/ProyectosAsignados', [
            'proyectos' => $evaluaciones
        ]);
    }

    // Descargar documentos del equipo como .rar
    public function descargarDocumentosEquipo($equipoId)
    {
        $carpetaRelativa = "equipos/{$equipoId}";
        // Obtener todos los archivos, incluyendo subcarpetas
        $archivos = Storage::disk('public')->allFiles($carpetaRelativa);

        if (count($archivos) === 0) {
            abort(404, 'No hay documentos para este equipo.');
        }

        $zipFileName = "equipo_{$equipoId}_documentos.zip";
        $zipTempPath = storage_path("app/temp/{$zipFileName}");

        // Crear carpeta temporal si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        // Eliminar archivo temporal anterior si existe
        if (file_exists($zipTempPath)) {
            unlink($zipTempPath);
        }

        // Crear el zip
        $zip = new \ZipArchive();
        if ($zip->open($zipTempPath, \ZipArchive::CREATE) !== true) {
            abort(500, 'No se pudo crear el archivo ZIP.');
        }

        foreach ($archivos as $archivo) {
            $absolutePath = storage_path('app/public/' . $archivo);
            // Guardar la ruta relativa dentro del zip (quitando el prefijo 'equipos/{id}/')
            $relativePathInZip = substr($archivo, strlen($carpetaRelativa) + 1);
            $zip->addFile($absolutePath, $relativePathInZip);
        }
        $zip->close();

        if (!file_exists($zipTempPath)) {
            abort(500, 'No se pudo crear el archivo ZIP.');
        }

        return response()->download($zipTempPath, $zipFileName)->deleteFileAfterSend(true);
    }

    //Criterios

    public function criterios()
    {
        $user = Auth::user();
        
        // Obtener concursos donde el usuario es evaluador
        $concursosIds = Evaluaciones::where('evaluador_id', $user->id)
            ->with('equipo.concurso')
            ->get()
            ->pluck('equipo.concurso_id')
            ->unique()
            ->filter()
            ->values();
        
        // Obtener criterios de evaluación
        $criterios = CriteriosEvaluacion::with(['modalidad'])
            ->whereIn('concurso_id', $concursosIds)
            ->orderBy('modalidad_id')
            ->orderBy('tipo_criterio')
            ->orderBy('id')
            ->get()
            ->map(function ($criterio) {
                return [
                    'id' => $criterio->id,
                    'nombre' => $criterio->nombre,
                    'puntaje_maximo' => (float) $criterio->puntaje_maximo,
                    'tipo_criterio' => $criterio->tipo_criterio,
                    'modalidad' => $criterio->modalidad->nombre ?? 'General'
                ];
            });
    
        return Inertia::render('ConcursosLayouts/Criterios', [
            'criterios' => $criterios
        ]);
    }

    //Reportes
    public function reportes()
    {
        $user = Auth::user();
        
        $evaluaciones = Evaluaciones::with([
            'equipo.proyecto.modalidad',
            'equipo.concurso',
            'puntajes'
        ])
        ->where('evaluador_id', $user->id)
        ->get()
        ->map(function ($evaluacion) {
            // Calcular puntaje total normalizado (0-100)
            $puntajeTotal = $evaluacion->puntajes->sum('puntaje_obtenido');
            $maximoPosible = $evaluacion->equipo->concurso->criteriosEvaluacion
                ->where('modalidad_id', $evaluacion->equipo->proyecto->modalidad_id)
                ->sum('puntaje_maximo');
            
            $puntajeNormalizado = $maximoPosible > 0 ? ($puntajeTotal / $maximoPosible) * 100 : 0;

            return [
                'id' => $evaluacion->id,
                'nombre_proyecto' => $evaluacion->equipo->proyecto->nombre,
                'modalidad_nombre' => $evaluacion->equipo->proyecto->modalidad->nombre,
                'modalidad_id' => $evaluacion->equipo->proyecto->modalidad_id,
                'concurso_nombre' => $evaluacion->equipo->concurso->nombre,
                'concurso_id' => $evaluacion->equipo->concurso_id,
                'estado' => $evaluacion->estado,
                'puntaje_total' => round($puntajeNormalizado, 2),
                'fecha_evaluacion' => $evaluacion->updated_at->format('d/m/Y'),
                'created_at' => $evaluacion->created_at->format('d/m/Y')
            ];
        });

        return Inertia::render('ConcursosLayouts/Reportes', [
            'evaluaciones' => $evaluaciones
        ]);
    }

}