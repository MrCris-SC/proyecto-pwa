<?php

namespace App\Http\Controllers;

use App\Models\Concursos;
use App\Models\Modalidades;
use App\Models\Participantes;
use Illuminate\Http\Request;
use App\Models\Linea;
use App\Models\Equipo;
use App\Models\Proyectos;
use App\Models\Asesores;
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
            'nombre', 'categoria', 'modalidad_id', 'linea_investigacion_id', 'concurso_id','perfil_jurado'
            ]));

            $equipoId = Equipo::generarCodigoEquipo();
            $equipo = Equipo::create([
            'id' => $equipoId,
            'proyecto_id' => $proyecto->id,
            'concurso_id' => $request->concurso_id,
            ]);

            // Actualizar el proyecto con la ID del equipo
            $proyecto->equipo_id = $equipo->id;
            $proyecto->save();

            

            // Crear los participantes del equipo
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

         // Obtener el valor de foregcheck desde la tabla equipos
         $asesorescheck = $proyecto && $proyecto->equipo ? $proyecto->equipo->asesorescheck : false;


        return Inertia::render('ConcursosLayouts/GestionProyectos', [
            'inscrito' => $inscrito,
            'concursoId' => $user->concurso_registrado_id,
            'proyecto' => $proyecto,
            'asesorescheck' => $asesorescheck, // Pasar foregcheck a la vista
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
        $validator = Validator::make($request->all(), [
            'asesorTecnico.nombre' => 'nullable|string|max:255',
            'asesorTecnico.tipo' => 'nullable|string',
            'asesorTecnico.clavePresupuestal' => 'nullable|string|max:255',
            'asesorTecnico.nivelAcademico' => 'nullable|string|max:255',
            'asesorTecnico.correo' => 'nullable|email|max:255',
            'asesorTecnico.telefono' => 'nullable|string|max:15',
            'asesorMetodologico.nombre' => 'nullable|string|max:255',
            'asesorMetodologico.nivelAcademico' => 'nullable|string|max:255',
            'asesorMetodologico.correo' => 'nullable|email|max:255',
            'asesorMetodologico.telefono' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $equipoId = Auth::user()->equipo_id;

            if ($request->filled('asesorTecnico.nombre')) {
                Asesores::create([
                    'nombre' => $request->asesorTecnico['nombre'],
                    'email' => $request->asesorTecnico['correo'],
                    'telefono' => $request->asesorTecnico['telefono'],
                    'tipo_asesor' => $request->asesorTecnico['tipo'],
                    'clave_presupuestal' => $request->asesorTecnico['clavePresupuestal'],
                    'nivel_academico' => $request->asesorTecnico['nivelAcademico'],
                    'equipo_id' => $equipoId,               
                ]);
            }

            if ($request->filled('asesorMetodologico.nombre')) {
                Asesores::create([
                    'nombre' => $request->asesorMetodologico['nombre'],
                    'email' => $request->asesorMetodologico['correo'],
                    'telefono' => $request->asesorMetodologico['telefono'],
                    'tipo_asesor' => 'Metodológico',
                    'nivel_academico' => $request->asesorMetodologico['nivelAcademico'],
                    'equipo_id' => $equipoId,
                ]);
            }

           

            // Actualizar el campo asesorescheck a true en la tabla equipo
            $equipo = Equipo::find($equipoId);
            $equipo->asesorescheck = true;
            $equipo->save();

            return redirect()->route('dashboard')->with('success', 'Asesores registrados exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al registrar asesores: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al registrar asesores.');
        }
    }
    
    public function subirDocumento(Request $request, $proyectoId)
    {
        $validator = Validator::make($request->all(), [
            'documentos.*' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $proyecto = Proyectos::findOrFail($proyectoId);
            $equipoId = $proyecto->equipo_id;

            // Crear la carpeta con el ID del equipo si no existe
            $equipoFolder = storage_path("app/public/equipos/{$equipoId}");
            if (!file_exists($equipoFolder)) {
                mkdir($equipoFolder, 0777, true);
            }

            // Guardar los documentos en la carpeta del equipo
            foreach ($request->file('documentos') as $index => $documento) {
                $documento->move($equipoFolder, $documento->getClientOriginalName());
            }

            return response()->json(['success' => 'Documentos subidos exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al subir documentos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al subir documentos.'], 500);
        }
    }
}