<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use App\Models\ResultadosFinales;
use Illuminate\Support\Facades\Log;
class ResultadosFinalesController extends Controller
{
    
  
public function generarPDF()
{
    $resultados = ResultadosFinales::with([
        'equipo.proyecto.modalidad',
        'equipo.proyecto',
        'equipo.participantes'
    ])->get();

    $resultadosAgrupados = $resultados->groupBy(function ($item) {
        $categoria = $item->equipo->proyecto->categoria ?? 'Sin categoría';
        $modalidad = optional($item->equipo->proyecto->modalidad)->nombre ?? 'Sin modalidad';
        return "{$categoria} - {$modalidad}";
    });

    $modalidadesAgrupadas = [];

    foreach ($resultadosAgrupados as $grupo => $items) {
        $filtrados = $items->filter(function ($r) {
            return $r->equipo &&
                   $r->equipo->proyecto &&
                   strtolower($r->equipo->proyecto->estado) !== 'descalificado';
        })->sortByDesc('promedio_final')->values();

        $modalidadesAgrupadas[$grupo] = [
            'podio' => $filtrados->take(3),
            'resultados' => $filtrados
        ];
    }

    $pdf = PDF::loadView('pdf.resultados_finales', [
        'modalidadesAgrupadas' => $modalidadesAgrupadas,
    ]);

    return $pdf->stream('resultados.pdf');
}


}
