<?php

namespace App\Http\Controllers;

use App\Models\Concursos;
use App\Models\Modalidades;
use App\Models\Participantes;
use Illuminate\Http\Request;
use App\Models\Linea;
use App\Models\Equipo;
use App\Models\Proyectos;
use App\Models\Concurso; // Importar el modelo Concurso
use Illuminate\Support\Facades\Log; // Importar la clase Log
use Illuminate\Support\Facades\Auth;

class ProyectosController extends Controller
{
    public function index()
    {
        $modalidades = Modalidades::all();
        $lineas = Linea::all();
        $concursos = Concursos::all(); // Obtener todos los concursos
        return response()->json(['modalidades' => $modalidades, 'lineas' => $lineas, 'concursos' => $concursos]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string',
            'modalidad_id' => 'required|exists:modalidades,id',
            'linea_investigacion_id' => 'required|exists:lineas_investigacion,id',
            'concurso_id' => 'nullable|exists:concursos,id',
            'equipo' => 'required|array',
            'equipo.*.nombre' => 'required|string|max:255'
        ]);

        // Crear el proyecto
        $proyecto = Proyectos::create($request->only(['nombre', 'categoria', 'modalidad_id', 'linea_investigacion_id', 'concurso_id']));
        Log::info('Proyecto creado', ['proyecto_id' => $proyecto->id]);

         // Generar el ID del equipo
    $equipoId = Equipo::generarCodigoEquipo();

    // Crear el equipo asociado al proyecto con un ID generado
    $equipo = Equipo::create([
        'id' => $equipoId,
        'proyecto_id' => $proyecto->id
    ]);
    Log::info('Equipo creado', ['equipo_id' => $equipo->id]);

    // Crear los integrantes del equipo
    foreach ($request->equipo as $integranteData) {
        Log::info('Creando integrante', ['equipo_id' => $equipo->id, 'nombre' => $integranteData['nombre']]);
        Participantes::create([
            'equipo_id' => $equipo->id,
            'nombre' => $integranteData['nombre']
        ]);
    }
    
    if (auth()->user()->rol === 'lider' && $request->concurso_id) {
        auth()->user()->update(['concurso_registrado_id' => $request->concurso_id]);
    }
        //return response()->json(['id' => $proyecto->id]);
        // Redirigir al usuario a /concursos
        return redirect('/concursos');
    }
}
