<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Evaluaciones;
use App\Models\Equipo;
use App\Models\Criterio;
use App\Models\PuntajesEvaluacion;
use App\Models\Concursos;
use App\Models\Linea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EvaluadorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $evaluacionesPendientes = Evaluaciones::with([
            'equipo.proyecto',
            'equipo.concurso.criterios' => function($query) use ($user) {
                $proyectoIds = Evaluaciones::where('evaluador_id', $user->id)
                    ->where('estado', 'pendiente')
                    ->with('equipo.proyecto')
                    ->get()
                    ->pluck('equipo.proyecto.linea_investigacion_id')
                    ->unique()
                    ->filter()
                    ->toArray();
                
                $query->whereIn('linea_investigacion_id', $proyectoIds);
            }
        ])
        ->where('evaluador_id', $user->id)
        ->where('estado', 'pendiente')
        ->get()
        ->map(function ($evaluacion) {
            $lineaId = $evaluacion->equipo->proyecto->linea_investigacion_id ?? null;
            
            $modalidad = $evaluacion->equipo->proyecto->modalidad ?? '';
            $modalidad = $this->parseModalidad($modalidad);
            
            return [
                'id' => $evaluacion->id,
                'nombre_proyecto' => $evaluacion->equipo->proyecto->nombre ?? 'Proyecto sin nombre',
                'modalidad' => $modalidad,
                'linea_investigacion' => $this->getNombreLineaInvestigacion($lineaId),
                'linea_investigacion_id' => $lineaId,
                'criterios' => $evaluacion->equipo->concurso->criterios
                    ->filter(function($criterio) use ($lineaId) {
                        return $criterio->linea_investigacion_id == $lineaId;
                    })
                    ->map(function ($criterio) {
                        return [
                            'id' => $criterio->id,
                            'nombre' => $criterio->nombre,
                            'puntaje_maximo' => (float) $criterio->puntaje_maximo,
                            'descripcion' => $criterio->descripcion
                        ];
                    })
                    ->values()
                    ->toArray()
            ];
        });
    
        return Inertia::render('ConcursosLayouts/Evaluacion', [
            'evaluacionesPendientes' => $evaluacionesPendientes,
            'lineasInvestigacion' => $this->getLineasInvestigacion()
        ]);
    }

    public function criterios()
    {
        return Inertia::render('ConcursosLayouts/Criterios', [
            'criterios' => Criterio::all(),
            'lineasInvestigacion' => Linea::all()
        ]);
    }

    public function proyectosAsignados()
    {
        $evaluaciones = Evaluaciones::with([
            'equipo.proyecto', 
            'equipo.concurso', 
            'puntajes.criterio'
        ])
        ->where('evaluador_id', Auth::id())
        ->get()
        ->map(function ($evaluacion) {
            $modalidad = $evaluacion->equipo->proyecto->modalidad ?? '';
            $modalidad = $this->parseModalidad($modalidad);
            
            return [
                'id' => $evaluacion->id,
                'proyecto' => [
                    'id' => $evaluacion->equipo->proyecto->id ?? null,
                    'nombre' => $evaluacion->equipo->proyecto->nombre ?? 'Proyecto sin nombre',
                    'tipo' => $evaluacion->equipo->proyecto->tipo ?? '',
                    'modalidad' => $modalidad,
                    'linea_investigacion' => $this->getNombreLineaInvestigacion($evaluacion->equipo->proyecto->linea_investigacion_id ?? null),
                    'linea_investigacion_id' => $evaluacion->equipo->proyecto->linea_investigacion_id ?? null
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
                        'puntaje' => (float) $puntaje->puntaje_obtenido,
                        'criterio' => [
                            'id' => $puntaje->criterio->id ?? null,
                            'nombre' => $puntaje->criterio->nombre ?? '',
                            'puntaje_maximo' => (float) $puntaje->criterio->puntaje_maximo ?? 0
                        ]
                    ];
                })
            ];
        });

        return Inertia::render('ConcursosLayouts/ProyectosAsignados', [
            'evaluaciones' => $evaluaciones
        ]);
    }

    public function guardarEvaluacion(Request $request, $evaluacionId)
    {
        $validated = $request->validate([
            'puntajes' => 'required|array',
            'puntajes.*.criterio_id' => 'required|exists:criterios_evaluacion,id',
            'puntajes.*.puntaje' => 'required|numeric|min:0',
            'comentarios' => 'nullable|string',
            'es_borrador' => 'required|boolean'
        ]);
        
        $evaluacion = Evaluaciones::findOrFail($evaluacionId);
        
        DB::transaction(function () use ($evaluacion, $validated) {
            if (!$validated['es_borrador']) {
                PuntajesEvaluacion::where('evaluacion_id', $evaluacion->id)->delete();
            }
         
            foreach ($validated['puntajes'] as $puntajeData) {
                PuntajesEvaluacion::updateOrCreate(
                    [
                        'evaluacion_id' => $evaluacion->id,
                        'criterio_id' => $puntajeData['criterio_id']
                    ],
                    [
                        'puntaje_obtenido' => (float) $puntajeData['puntaje'],
                        'comentario' => $validated['comentarios'] ?? null
                    ]
                );
            }
            
            if (!$validated['es_borrador']) {
                $evaluacion->estado = 'completada';
                $evaluacion->save();
            }
        });
    
        $message = $validated['es_borrador'] 
            ? 'Borrador guardado exitosamente' 
            : 'Evaluación final enviada exitosamente';
        
        return redirect()->back()->with('success', $message);
    }

    private function parseModalidad($modalidad)
    {
        if (is_string($modalidad)) {
            if (strpos($modalidad, '{') === 0) {
                try {
                    $modalidadData = json_decode($modalidad, true);
                    return $modalidadData['nombre'] ?? $modalidad;
                } catch (\Exception $e) {
                    return $modalidad;
                }
            }
            return $modalidad;
        }
        
        if (is_array($modalidad) || is_object($modalidad)) {
            return $modalidad['nombre'] ?? $modalidad;
        }
        
        return $modalidad;
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