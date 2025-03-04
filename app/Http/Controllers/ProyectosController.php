<?php

namespace App\Http\Controllers;

use App\Models\Concursos;
use App\Models\Modalidades;
use App\Models\Participantes;
use Illuminate\Http\Request;
use App\Models\Linea;
use App\Models\Equipo;
use App\Models\Proyectos;
use App\Models\Asesor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;


class ProyectosController extends Controller
{
    public function index()
    {
        $modalidades = Modalidades::all();
        $lineas = Linea::all();
        $concursos = Concursos::all();

        return response()->json([
            'modalidades' => $modalidades,
            'lineas' => $lineas,
            'concursos' => $concursos,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string',
            'modalidad_id' => 'required|exists:modalidades,id',
            'linea_investigacion_id' => 'required|exists:lineas_investigacion,id',
            'concurso_id' => 'nullable|exists:concursos,id',
            'equipo' => 'required|array|min:1',
            'equipo.*.nombre' => 'required|string|max:255',
            'equipo.*.correo' => 'required|email|max:255',
            'equipo.*.genero' => 'required|in:femenino,masculino',
            'equipo.*.telefono' => 'required|string|max:15',
            'equipo.*.direccion' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $proyecto = Proyectos::create($request->only([
                'nombre', 'categoria', 'modalidad_id', 'linea_investigacion_id', 'concurso_id',
            ]));

            $equipoId = Equipo::generarCodigoEquipo();
            $equipo = Equipo::create([
                'id' => $equipoId,
                'proyecto_id' => $proyecto->id,
                'concurso_id' => $request->concurso_id,
            ]);

            foreach ($request->equipo as $integranteData) {
                Participantes::create([
                    'equipo_id' => $equipo->id,
                    'nombre' => $integranteData['nombre'],
                    'correo' => $integranteData['correo'],
                    'genero' => $integranteData['genero'],
                    'telefono' => $integranteData['telefono'],
                    'direccion' => $integranteData['direccion'],
                ]);
            }

            $authUser = Auth::user();
            $authUser->update(['equipo_id' => $equipo->id]);

            if (Auth::user()->rol === 'lider' && $request->concurso_id) {
                Auth::user()->update(['concurso_registrado_id' => $request->concurso_id]);
            }

            return redirect()->route('dashboard')->with('success', 'Proyecto creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear el proyecto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el proyecto.'], 500);
        }
    }

    public function generarPDF()
    {
        try {
            $authUser = Auth::user();
            $equipo = Equipo::with(['proyecto', 'participantes', 'proyecto.concurso'])
                            ->where('id', $authUser->equipo_id)
                            ->first();

            if (!$equipo) {
                return redirect()->back()->with('error', 'Equipo no encontrado.');
            }

            $pdf = Pdf::loadView('pdf.proyecto', ['equipo' => $equipo]);
            return $pdf->download('equipo.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el PDF.');
        }
    }

    public function gestionProyectos()
    {
        $user = Auth::user();

        // Verificar si el usuario está inscrito en un concurso
        $inscrito = $user->concurso_registrado_id !== null;

        $proyecto = Proyectos::with(['equipo.participantes', 'concurso'])
            ->whereHas('equipo', function ($query) use ($user) {
                $query->where('id', $user->equipo_id);
            })
            ->first();

        return Inertia::render('ConcursosLayouts/GestionProyectos', [
            'inscrito' => $inscrito,
            'concursoId' => $user->concurso_registrado_id,
            'proyecto' => $proyecto,
        ]);
    }

    public function inscribirse(Request $request, $concursoId)
    {
        $user = Auth::user();

        // Verificar si el usuario ya está inscrito en un concurso
        if ($user->concurso_registrado_id !== null) {
            return redirect()->back()->with('error', 'Ya estás inscrito en un concurso.');
        }

        // Inscribir al usuario en el concurso
        $user->concurso_registrado_id = $concursoId;
        $user->save();

        return redirect()->route('gestion.proyectos')->with('success', 'Inscripción exitosa.');
    }

    public function registrarAsesor(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:asesores,email',
            'telefono' => 'required|string|max:15',
            'tipo_asesor' => 'required|in:Técnico,Metodológico',
            'clave_presupuestal' => 'nullable|string|max:255',
            'nivel_academico' => 'required|string|max:255',
            'perfiles_jurado' => 'nullable|string|max:255',
            'equipo_id' => 'required|exists:equipos,id',
        ]);
    
        // Crear el asesor
        Asesor::create($request->all());
    
        return response()->json(['message' => 'Asesor registrado correctamente.'], 201);
    }
}