<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Equipo;
use App\Models\Evaluaciones;
use App\Models\Proyectos;
use App\Models\User;
use App\Models\ResultadosFinales;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de tener barryvdh/laravel-dompdf instalado

class ConcursosFinales extends Controller
{
    public function equiposConParticipantes($concursoId)
    {
        $equipos = Equipo::with(['proyecto', 'participantes'])
            ->where('concurso_id', $concursoId)
            ->get();

        return response()->json(['equipos' => $equipos]);
    }

    public function evaluacionesResumenProyecto($proyectoId)
    {
        // Busca el equipo asociado al proyecto
        $equipo = Equipo::where('proyecto_id', $proyectoId)->first();
        if (!$equipo) {
            return response()->json(['resumen' => []]);
        }

        // Obtiene el resultado final del equipo
        $resultadoFinal = ResultadosFinales::where('equipo_id', $equipo->id)->first();
        $promedioFinal = $resultadoFinal ? $resultadoFinal->promedio_final : null;

        // Obtiene las evaluaciones del equipo
        $evaluaciones = Evaluaciones::where('equipo_id', $equipo->id)
            ->with(['evaluador'])
            ->get();

        $resumen = $evaluaciones->map(function($ev) {
            $puntajeTotal = method_exists($ev, 'puntajes') && $ev->puntajes ? $ev->puntajes->sum('puntaje') : null;
            return [
                'evaluador_nombre' => $ev->evaluador ? ($ev->evaluador->nombre ?? $ev->evaluador->name ?? 'Sin nombre') : 'Sin evaluador',
                'estado' => $ev->estado,
                'puntaje_total' => $puntajeTotal,
                'comentarios' => $ev->comentarios,
            ];
        });

        // Devuelve también el promedio_final
        return response()->json([
            'resumen' => $resumen,
            'promedio_final' => $promedioFinal
        ]);
    }

    public function cambiarEstadoProyecto(Request $request, $proyectoId): JsonResponse
    {
        $request->validate([
            'estado' => 'required|string'
        ]);
        $proyecto = Proyectos::findOrFail($proyectoId);
        $proyecto->estado = $request->estado;
        $proyecto->save();

        return response()->json(['success' => true, 'estado' => $proyecto->estado]);
    }

    // Nueva función para obtener participantes por concurso
    public function participantesPorConcurso($concursoId)
    {
        // Obtiene los equipos del concurso
        $equipos = Equipo::where('concurso_id', $concursoId)->pluck('id');
        // Obtiene los participantes de esos equipos
        $participantes = \App\Models\Participantes::whereIn('equipo_id', $equipos)
            ->get(['id', 'nombre', 'equipo_id']);

        return response()->json(['participantes' => $participantes]);
    }

    public function descargarReporteEquipos($concursoId)
    {
        $concurso = \App\Models\Concursos::findOrFail($concursoId);
        $equipos = \App\Models\Equipo::with(['proyecto', 'participantes', 'resultadoFinal'])
            ->where('concurso_id', $concursoId)
            ->get();

        $pdf = Pdf::loadView('pdf.reporte_equipos', [
            'concurso' => $concurso,
            'equipos' => $equipos
        ]);

        return $pdf->download('reporte_equipos_' . $concurso->nombre . '.pdf');
    }
}
