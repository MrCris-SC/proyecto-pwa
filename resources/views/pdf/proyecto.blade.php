<!DOCTYPE html>
<html>
<head>
    <title>Registro de Proyecto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between; /* Space between logos */
            align-items: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .header-logo {
            width: 300px; /* Increase the size of the image */
            height: auto;
        }
        .header-logo2 {
            width: 300px; /* Increase the size of the image */
            height: auto;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .participant {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .photo-box {
            width: 80px;
            height: 80px;
            border: 1px solid #000;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .participant-info {
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="header-logo">
        <img src="{{ public_path('images/logo2.png') }}" alt="Logo" class="header-logo2">
    </div>

    
    <div class="section">
        <h2>Nombre del Concurso:</h2>
        <p><strong>Nombre:</strong> {{ $equipo->proyecto->concurso->nombre ?? 'No especificado' }}</p>
    </div>
    
    <div class="section">
        <h2>Registro de Proyecto</h2>
        <p><strong>Número de Registro:</strong> {{ $equipo->proyecto->id }}</p>
        <p><strong>Nombre del Proyecto:</strong> {{ $equipo->proyecto->nombre }}</p>
        <p><strong>Modalidad:</strong> {{ $equipo->proyecto->modalidad_id }}</p>
    </div>
    
    <div class="section">
        <h2>Integrantes del Equipo</h2>
        @foreach ($equipo->participantes as $participante)
            <div class="participant">
                <div class="photo-box">Foto</div>
                <div class="participant-info">
                    <p><strong>Nombre:</strong> {{ $participante->nombre }}</p>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="section">
    <h3>Asesores</h3>
    @php
        $tiposAsesor = ['Técnico', 'Metodológico'];
    @endphp
    @foreach ($tiposAsesor as $tipo)
        @php
            $asesor = $equipo->asesores->firstWhere('tipo_asesor', $tipo);
        @endphp
        <p><strong>Tipo:</strong> {{ $tipo }}</p>
        @if ($asesor)
            <p><strong>Nombre:</strong> {{ $asesor->nombre }}</p>
            <p><strong>Correo:</strong> {{ $asesor->email }}</p>
            <p><strong>Teléfono:</strong> {{ $asesor->telefono }}</p>
        @else
            <p>No especificado</p>
        @endif
        <hr>
    @endforeach
    </div>
</body>
</html>
