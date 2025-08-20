<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resultados Finales</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h1, h2 { color: #222; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        th, td { border: 1px solid #888; padding: 6px 10px; text-align: left; }
        th { background: #f8f8f8; }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Resultados Finales</h1>

    @foreach($modalidadesAgrupadas as $modalidad => $data)
        <h2>{{ $modalidad }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Proyecto</th>
                    <th>Categoría</th>
                    <th>Promedio Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['resultados'] as $idx => $res)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $res->equipo->proyecto->nombre ?? 'Sin nombre' }}</td>
                        <td>
                            @php
                                $cat = $res->equipo->proyecto->categoria ?? '';
                                echo $cat === 'alumno' ? 'Alumno' : ($cat === 'docente' ? 'Docente' : ucfirst($cat));
                            @endphp
                        </td>
                        <td>{{ $res->promedio_final }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>

