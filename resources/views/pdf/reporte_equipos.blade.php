<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Equipos - {{ $concurso->nombre ?? '' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .estado-en-orden { color: #155724; background: #d4edda; }
        .estado-descalificado { color: #721c24; background: #f8d7da; }
        .integrantes-list { margin: 0; padding-left: 18px; }
        .promedio-final { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Reporte de Equipos</h1>
    <h2>Concurso: {{ $concurso->nombre ?? '' }}</h2>
    <table>
        <thead>
            <tr>
                <th># Equipo</th>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Integrantes</th>
                <th>Promedio Final</th>
            </tr>
        </thead>
        <tbody>
        @foreach($equipos as $equipo)
            <tr>
                <td>{{ $equipo->id }}</td>
                <td>
                    {{ $equipo->proyecto->nombre ?? 'Sin proyecto' }}
                </td>
                <td class="{{ ($equipo->proyecto->estado ?? '') === 'Descalificado' ? 'estado-descalificado' : 'estado-en-orden' }}">
                    {{ $equipo->proyecto->estado ?? 'Sin estado' }}
                </td>
                <td>
                    <ul class="integrantes-list">
                        @foreach($equipo->participantes as $p)
                            <li>{{ $p->nombre }} @if($p->telefono) ({{ $p->telefono }}) @endif</li>
                        @endforeach
                    </ul>
                </td>
                <td class="promedio-final">
                    {{ $equipo->resultadoFinal->promedio_final ?? 'N/A' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p>Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
</body>
</html>
