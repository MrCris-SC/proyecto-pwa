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
                'puntaje_obtenido' => $puntaje,
                'comentario' => $comentario
            ]
        );
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
}