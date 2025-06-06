<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Concursos;
use App\Models\Evaluaciones;
use Inertia\Inertia;

class EvaluacionesManualesController extends Controller
{
    public function datos($concursoId)
    {
        try {
            // Carga el concurso con equipos y evaluadores usando las relaciones del modelo
            $concurso = Concursos::with([
                'equipos:id,concurso_id,proyecto_id',
                'evaluadores:id,name'
            ])->findOrFail($concursoId);

            $equipos = $concurso->equipos;
            $evaluadores = $concurso->evaluadores;

            // Obtener las evaluaciones de los equipos de este concurso
            $evaluaciones = [];
            if ($equipos->count() > 0) {
                $evaluaciones = Evaluaciones::whereIn('equipo_id', $equipos->pluck('id'))
                    ->get(['id', 'equipo_id', 'evaluador_id', 'estado', 'comentarios']);
            }

            return response()->json([
                'equipos' => $equipos,
                'evaluadores' => $evaluadores,
                'evaluaciones' => $evaluaciones,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error en EvaluacionesManualesController@datos: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor.'], 500);
        }
    }

    public function evaluadoresAsignadosEquipoLider()
    {
        $user = auth()->user();
        if ($user->rol !== 'lider' || !$user->equipo_id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado o sin equipo asignado.'
            ], 403);
        }

        // Obtener evaluaciones del equipo del líder, con evaluador y perfil
        $evaluaciones = Evaluaciones::with('evaluador')
            ->where('equipo_id', $user->equipo_id)
            ->get();

        $evaluadores = [];
        foreach ($evaluaciones as $evaluacion) {
            if ($evaluacion->evaluador) {
                $perfil = \App\Models\Evaluadores::where('userID', $evaluacion->evaluador->id)->value('perfil');
                $evaluadores[] = [
                    'id' => $evaluacion->evaluador->id,
                    'nombre' => $evaluacion->evaluador->name,
                    'perfil' => is_string($perfil) ? json_decode($perfil, true) : $perfil,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'evaluadores' => $evaluadores,
        ]);
    }

    public function vistaEvaluadoresAsignados()
    {
        $user = auth()->user();
        if ($user->rol !== 'lider' || !$user->equipo_id) {
            return redirect()->route('dashboard')->with('error', 'No autorizado o sin equipo asignado.');
        }

        $evaluaciones = Evaluaciones::with('evaluador')
            ->where('equipo_id', $user->equipo_id)
            ->get();

        $evaluadores = [];
        foreach ($evaluaciones as $evaluacion) {
            if ($evaluacion->evaluador) {
                $perfil = \App\Models\Evaluadores::where('userID', $evaluacion->evaluador->id)->value('perfil');
                $evaluadores[] = [
                    'id' => $evaluacion->evaluador->id,
                    'nombre' => $evaluacion->evaluador->name,
                    'perfil' => is_string($perfil) ? json_decode($perfil, true) : $perfil,
                ];
            }
        }

        return Inertia::render('ConcursosLayouts/EvaluadoresAsignados', [
            'evaluadores' => $evaluadores,
        ]);
    }
}
