<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Evaluaciones - {{ $concurso->nombre ?? 'Concurso' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #222; margin: 40px; }
        h1, h2, h3 { color: #611232; }
        .header { text-align: center; margin-bottom: 30px; }
        .equipo-section { margin-bottom: 40px; }
        .equipo-title { font-size: 1.1em; font-weight: bold; color: #8A1C4A; margin-bottom: 8px; }
        .integrantes { margin-bottom: 8px; }
        .evaluador-block { border: 1px solid #8A1C4A; border-radius: 8px; margin-bottom: 16px; padding: 12px; }
        .evaluador-title { font-weight: bold; color: #8A1C4A; margin-bottom: 6px; }
        .puntajes-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .puntajes-table th, .puntajes-table td { border: 1px solid #ccc; padding: 4px 8px; text-align: left; }
        .puntajes-table th { background: #f3e6ee; }
        .contribucion { font-style: italic; color: #444; }
        .footer { text-align: center; font-size: 0.9em; color: #888; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Evaluaciones</h1>
        <h2>{{ $concurso->nombre ?? 'Concurso' }}</h2>
    </div>
    @foreach($equipos as $equipo)
        <div class="equipo-section">
            <div class="equipo-title">
                Equipo #{{ $equipo->id }} | Proyecto: {{ $equipo->proyecto->nombre ?? '-' }}
            </div>
            <div class="integrantes">
                <strong>Integrantes:</strong>
                @if($equipo->participantes && count($equipo->participantes))
                    @foreach($equipo->participantes as $p)
                        {{ $p->nombre }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                @else
                    Sin integrantes
                @endif
            </div>
            <div>
                <strong>Evaluaciones:</strong>
                @if(isset($equipo->evaluaciones) && count($equipo->evaluaciones))
                    @foreach($equipo->evaluaciones as $idx => $evaluacion)
                        <div class="evaluador-block">
                            <div class="evaluador-title">
                                Evaluador {{ $idx + 1 }}
                                @if(!empty($evaluacion->perfil))
                                    &mdash; Perfiles:
                                    @foreach((array)$evaluacion->perfil as $p)
                                        {{ $p }}{{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                @endif
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
                                        <td colspan="2">
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
                    @endforeach
                @else
                    <div>No hay evaluaciones registradas para este equipo.</div>
                @endif
            </div>
        </div>
    @endforeach
    <div class="footer">
        <p>Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
