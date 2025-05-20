<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Participación en Concurso</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #222; margin: 40px; }
        h1, h2, h3 { color: #611232; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 30px; }
        .evaluador-block { border: 1px solid #8A1C4A; border-radius: 8px; margin-bottom: 20px; padding: 16px; }
        .evaluador-title { font-size: 1.1em; font-weight: bold; color: #8A1C4A; margin-bottom: 8px; }
        .puntajes-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .puntajes-table th, .puntajes-table td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        .puntajes-table th { background: #f3e6ee; }
        .contribucion { font-style: italic; color: #444; }
        .footer { text-align: center; font-size: 0.9em; color: #888; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resumen de Participación en Concurso</h1>
        <h2>{{ $concurso->nombre ?? 'Concurso' }}</h2>
        <p><strong>Equipo:</strong> {{ $equipo->id }} | <strong>Proyecto:</strong> {{ $equipo->proyecto->nombre ?? '-' }}</p>
        <p><strong>Integrantes:</strong>
            @foreach($equipo->participantes as $p)
                {{ $p->nombre }}{{ !$loop->last ? ',' : '' }}
            @endforeach
        </p>
    </div>

    <div class="section">
        <h3>Evaluadores y Puntajes</h3>
        @forelse($evaluaciones as $evaluacion)
            <div class="evaluador-block">
                <div class="evaluador-title">
                    Evaluador: {{ $evaluacion->evaluador->name ?? 'N/A' }}
                </div>
                <table class="puntajes-table">
                    <thead>
                        <tr>
                            <th>Criterio</th>
                            <th>Puntaje Asignado</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluacion->puntajes as $puntaje)
                            <tr>
                                <td>{{ $puntaje->criterio->nombre ?? '-' }}</td>
                                <td>{{ $puntaje->puntaje_obtenido }}</td>                                

                            </tr>
                            
                        @endforeach
                        <tr>
                                <td colspan="3">
                                    <strong>Comentario:</strong> {{ $evaluacion->comentarios ?? '-' }}
                                </td>
                        </tr>
                    </tbody>
                </table>
                <div class="contribucion">
                    <strong>Contribución total de este evaluador:</strong>
                    {{ $evaluacion->puntajes->sum('puntaje_obtenido') }}
                </div>
            </div>
        @empty
            <p>No hay evaluaciones registradas para este equipo.</p>
        @endforelse
    </div>

    <div class="footer">
        <p>Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>