<?php

namespace App\Http\Controllers;

use App\Models\Concursos;
use App\Models\Modalidades;
use App\Models\Participantes;
use Illuminate\Http\Request;
use App\Models\Linea;
use App\Models\Equipo;
use App\Models\Proyectos;
use App\Models\Concurso; // Verifica si este modelo es necesario
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Inertia\Inertia;


class ProyectosController extends Controller
{
    public function index()
    {
        $modalidades = Modalidades::all();
        $lineas = Linea::all();
        $concursos = Concursos::all(); // Datos de concursos

        return response()->json([
            'modalidades' => $modalidades, 
            'lineas' => $lineas, 
            'concursos' => $concursos
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string',
            'modalidad_id' => 'required|exists:modalidades,id',
            'linea_investigacion_id' => 'required|exists:lineas_investigacion,id', // Verifica que esta clave sea la correcta
            'concurso_id' => 'nullable|exists:concursos,id',
            'equipo' => 'required|array|min:1',
            'equipo.*.nombre' => 'required|string|max:255',
            'equipo.*.correo' => 'required|email|max:255',
            'equipo.*.genero' => 'required|in:femenino,masculino',
            'equipo.*.telefono' => 'required|string|max:15',
            'equipo.*.direccion' => 'required|string|max:255'
        ]);

        // Crear el proyecto
        $proyecto = Proyectos::create($request->only([
            'nombre', 'categoria', 'modalidad_id', 'linea_investigacion_id', 'concurso_id'
        ]));
        Log::info('Proyecto creado', ['proyecto_id' => $proyecto->id]);

        // Generar el ID del equipo
        $equipoId = Equipo::generarCodigoEquipo();

        // Crear el equipo asociado al proyecto
        $equipo = Equipo::create([
            'id' => $equipoId,
            'proyecto_id' => $proyecto->id,
            'concurso_id' => $request->concurso_id,
        ]);
        Log::info('Equipo creado', ['equipo_id' => $equipo->id]);

        // Registrar los integrantes del equipo
        foreach ($request->equipo as $integranteData) {
            Participantes::create([
                'equipo_id' => $equipo->id,
                'nombre' => $integranteData['nombre'],
                'correo' => $integranteData['correo'],
                'genero' => $integranteData['genero'],
                'telefono' => $integranteData['telefono'],
                'direccion' => $integranteData['direccion']
            ]);
        }

        // Si el usuario es líder, actualizar el concurso registrado
        if (Auth::user()->rol === 'lider' && $request->concurso_id) {
            Auth::user()->update(['concurso_registrado_id' => $request->concurso_id]);
        }

        // Redirigir a /concursos
        return redirect()->route('dashboard')->with('success', 'Proyecto creado exitosamente.');
    }

    public function generarPDF($id)
    {
        $proyecto = Proyectos::with('equipo', 'concurso')->findOrFail($id);
        $pdf = PDF::loadView('pdf.proyecto', compact('proyecto'));
        return $pdf->download('proyecto.pdf');
    }
}
