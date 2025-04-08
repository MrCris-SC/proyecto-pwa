<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Evaluaciones;
use App\Models\Equipo;
use App\Models\Criterio;
use App\Models\PuntajesEvaluacion;
use App\Models\Concursos;
use Illuminate\Support\Facades\Auth;

class EvaluadorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener evaluaciones pendientes del evaluador con relaciones necesarias
        $evaluacionesPendientes = Evaluaciones::with([
            'equipo.proyecto', 
            'equipo.concurso.criterios'
        ])
        ->where('evaluador_id', $user->id)
        ->where('estado', 'pendiente')
        ->get()
        ->map(function ($evaluacion) {
            return [
                'id' => $evaluacion->id,
                'nombre_proyecto' => $evaluacion->equipo->proyecto->nombre ?? 'Proyecto sin nombre',
                'tipo_proyecto' => $evaluacion->equipo->proyecto->tipo ?? '',
                'modalidad' => $evaluacion->equipo->proyecto->modalidad ?? '',
                'linea_investigacion' => $evaluacion->equipo->proyecto->linea_investigacion ?? '',
                'criterios' => $evaluacion->equipo->concurso->criterios->map(function ($criterio) {
                    return [
                        'id' => $criterio->id,
                        'nombre' => $criterio->nombre,
                        'puntaje_maximo' => $criterio->puntaje_maximo,
                        'descripcion' => $criterio->descripcion
                    ];
                }) ?? []
            ];
        });
    
        // Obtener todas las evaluaciones del evaluador (para la vista de proyectos asignados)
        $evaluaciones = Evaluaciones::with([
            'equipo.proyecto', 
            'equipo.concurso', 
            'puntajes.criterio'
        ])
        ->where('evaluador_id', $user->id)
        ->get()
        ->map(function ($evaluacion) {
            return [
                'id' => $evaluacion->id,
                'proyecto' => [
                    'id' => $evaluacion->equipo->proyecto->id ?? null,
                    'nombre' => $evaluacion->equipo->proyecto->nombre ?? 'Proyecto sin nombre',
                    'tipo' => $evaluacion->equipo->proyecto->tipo ?? '',
                    'modalidad' => $evaluacion->equipo->proyecto->modalidad ?? '',
                    'linea_investigacion' => $evaluacion->equipo->proyecto->linea_investigacion ?? ''
                ],
                'concurso' => [
                    'id' => $evaluacion->equipo->concurso->id ?? null,
                    'nombre' => $evaluacion->equipo->concurso->nombre ?? '',
                    'fase' => $evaluacion->equipo->concurso->fase ?? ''
                ],
                'estado' => $evaluacion->estado,
                'puntajes' => $evaluacion->puntajes->map(function ($puntaje) {
                    return [
                        'criterio_id' => $puntaje->criterio_id,
                        'puntaje' => $puntaje->puntaje,
                        'criterio' => [
                            'id' => $puntaje->criterio->id ?? null,
                            'nombre' => $puntaje->criterio->nombre ?? '',
                            'puntaje_maximo' => $puntaje->criterio->puntaje_maximo ?? 0
                        ]
                    ];
                })
            ];
        });
    
        return Inertia::render('ConcursosLayouts/Evaluacion', [
            'evaluacionesPendientes' => $evaluacionesPendientes,
            'evaluaciones' => $evaluaciones,
            'lineasInvestigacion' => $this->getLineasInvestigacion(),
            'concurso' => null // O puedes cargar el concurso actual si es relevante
        ]);
    }

    public function proyectosAsignados()
    {
        $user = Auth::user();
        
        $evaluaciones = Evaluaciones::with(['equipo.proyecto', 'equipo.concurso', 'puntajes'])
            ->where('evaluador_id', $user->id)
            ->get()
            ->map(function ($evaluacion) {
                return [
                    'id' => $evaluacion->id,
                    'proyecto' => [
                        'id' => $evaluacion->equipo->proyecto->id,
                        'nombre' => $evaluacion->equipo->proyecto->nombre,
                        'tipo' => $evaluacion->equipo->proyecto->tipo,
                        'modalidad' => $evaluacion->equipo->proyecto->modalidad,
                        'linea_investigacion' => $evaluacion->equipo->proyecto->linea_investigacion
                    ],
                    'concurso' => [
                        'id' => $evaluacion->equipo->concurso->id,
                        'nombre' => $evaluacion->equipo->concurso->nombre,
                        'fase' => $evaluacion->equipo->concurso->fase
                    ],
                    'estado' => $evaluacion->estado,
                    'puntajes' => $evaluacion->puntajes
                ];
            });

        return Inertia::render('ConcursosLayouts/ProyectosAsignados', [
            'evaluaciones' => $evaluaciones
        ]);
    }

    public function criterios($concursoId)
    {
        $concurso = Concursos::with('criterios')->findOrFail($concursoId);
        
        return Inertia::render('ConcursosLayouts/Criterios', [
            'concurso' => $concurso,
            'lineasInvestigacion' => $this->getLineasInvestigacion()
        ]);
    }

    public function guardarEvaluacion(Request $request, $evaluacionId)
    {
        $validated = $request->validate([
            'puntajes' => 'required|array',
            'puntajes.*.criterio_id' => 'required|exists:criterios,id',
            'puntajes.*.puntaje' => 'required|numeric|min:0',
            'comentarios' => 'nullable|string',
            'es_borrador' => 'required|boolean'
        ]);

        $evaluacion = Evaluaciones::findOrFail($evaluacionId);
        
        DB::transaction(function () use ($evaluacion, $validated) {
            // Eliminar puntajes anteriores si no es borrador
            if (!$validated['es_borrador']) {
                PuntajesEvaluacion::where('evaluacion_id', $evaluacion->id)->delete();
            }
            
            // Guardar nuevos puntajes
            foreach ($validated['puntajes'] as $puntajeData) {
                PuntajesEvaluacion::updateOrCreate(
                    [
                        'evaluacion_id' => $evaluacion->id,
                        'criterio_id' => $puntajeData['criterio_id']
                    ],
                    [
                        'puntaje' => $puntajeData['puntaje']
                    ]
                );
            }
            
            // Actualizar estado de evaluación
            if (!$validated['es_borrador']) {
                $evaluacion->estado = 'completada';
                $evaluacion->comentarios = $validated['comentarios'];
                $evaluacion->save();
            }
        });

        return redirect()->back()->with('success', 'Evaluación guardada exitosamente');
    }

    private function getLineasInvestigacion()
    {
        return [
            'Desarrollo tecnológico',
            'Investigación educativa',
            'Desarrollo sustentable y medio ambiente',
            'Investigación en ciencias de la salud',
            'Desarrollo humano, social y emocional'
        ];
    }
}