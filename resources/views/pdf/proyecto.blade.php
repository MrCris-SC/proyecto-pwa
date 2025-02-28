<!DOCTYPE html>
<html>
<head>
    <title>Equipo PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            margin-bottom: 10px;
        }
        .section p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detalles del Equipo</h1>
    </div>

    <div class="section">
        <h2>Proyecto</h2>
        <p><strong>Nombre:</strong> {{ $equipo->proyecto->nombre }}</p>
        <p><strong>Categoría:</strong> {{ $equipo->proyecto->categoria }}</p>
        <p><strong>Modalidad:</strong> {{ $equipo->proyecto->modalidad_id }}</p>
        <p><strong>Línea de Investigación:</strong> {{ $equipo->proyecto->linea_investigacion_id }}</p>
    </div>

    <div class="section">
        <h2>Concurso</h2>
        @if ($equipo->proyecto->concurso)
            <p><strong>Nombre:</strong> {{ $equipo->proyecto->concurso->nombre }}</p>
            <p><strong>Descripción:</strong> {{ $equipo->proyecto->concurso->descripcion }}</p>
        @else
            <p>No hay concurso asociado.</p>
        @endif
    </div>

    <div class="section">
        <h2>Integrantes del Equipo</h2>
        @foreach ($equipo->participantes as $participante)
            <p><strong>Nombre:</strong> {{ $participante->nombre }}</p>
            <p><strong>Correo:</strong> {{ $participante->correo }}</p>
            <p><strong>Género:</strong> {{ $participante->genero }}</p>
            <p><strong>Teléfono:</strong> {{ $participante->telefono }}</p>
            <p><strong>Dirección:</strong> {{ $participante->direccion }}</p>
            <hr>
        @endforeach
    </div>
</body>
</html>