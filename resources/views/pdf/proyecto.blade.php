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
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .header-logo,
        .header-logo2 {
            width: 300px;
            height: auto;
        }
        .concurso-nombre {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .titulo {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .registro-proyecto {
            font-size: 13px;
            line-height: 1;
        }
        .integrantes, .asesores {
            font-size: 12px;
            font-family: 'Arial', sans-serif;
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

    <div class="concurso-nombre">
        {{ $equipo->proyecto->concurso->nombre ?? 'No especificado' }}
    </div>

    <div class="titulo">
        <h2>Formato de registro FOREG</h2>
    </div>

    <div class="section registro-proyecto">
        <h2>Registro de Proyecto</h2>
        <p><strong>Número de Registro:</strong> {{ $equipo->id }}</p>
        <p><strong>Nombre del Proyecto:</strong> {{ $equipo->proyecto->nombre }}</p>
        <p><strong>Modalidad:</strong> {{ $equipo->proyecto->modalidad->nombre ?? 'No especificada' }}</p>
    </div>

    <div class="section integrantes">
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

    <div class="section asesores">
        <h3>Asesores</h3>
        @php
            $asesor_origenes = [
                'tecnico' => 'Técnico',
                'metodologico' => 'Metodológico',
            ];
        @endphp

        @foreach ($asesor_origenes as $clave => $etiqueta)
            @php
                $asesor = $equipo->asesores->firstWhere('asesor_origen', $clave);
            @endphp

            <p style="font-weight:bold; margin-bottom:4px;">{{ $etiqueta }}</p>

            @if ($asesor)
                <p><strong>Nombre:</strong> {{ $asesor->nombre }}</p>               
            @else
                <p>No registrado.</p>
            @endif

            <hr>
        @endforeach
    </div>

    <div style="margin-top:40px; font-size:12px;">
        <p><strong>Sello oficial del plantel:</strong> &nbsp;--------------------------------------</p>
        <p><strong>Nombre y firma del director del plantel:</strong> &nbsp;--------------------------------------</p>
        <p>Al requisitar estos datos autorizo la solicitud y captura de datos personales a razón de estadística científica e institucional, sin menoscabo de lo señalado en la Ley Federal de Transparencia y Acceso a la Información Pública Gubernamental. Asimismo, autorizo compartir los datos personales a las organizaciones nacionales e internacionales que coordinan los concursos, ferias y eventos de ciencia y tecnología, así como, para fines académicos y de promoción científica.</p>
        <p><strong>Fecha de impresión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>
</body>
</html>
