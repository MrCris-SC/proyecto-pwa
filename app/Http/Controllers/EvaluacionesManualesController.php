<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Concursos;
use App\Models\Evaluaciones;

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
}
