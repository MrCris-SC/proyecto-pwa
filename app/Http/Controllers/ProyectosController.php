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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

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
            // Obtener inicial de la categoría
            $categoriaInicial = strtoupper(substr($request->categoria, 0, 1));

            // Obtener inicial de la modalidad (última palabra principal)
            $modalidad = Modalidades::find($request->modalidad_id);
            $modalidadInicial = '';
            if ($modalidad) {
                $palabras = preg_split('/\s+/', trim($modalidad->nombre));
                // Buscar la última palabra principal (ignorando preposiciones comunes)
                $preposiciones = ['de', 'del', 'la', 'el', 'los', 'las', 'y', 'en', 'a', 'con', 'por', 'para', 'un', 'una'];
                for ($i = count($palabras) - 1; $i >= 0; $i--) {
                    if (!in_array(strtolower($palabras[$i]), $preposiciones)) {
                        $modalidadInicial = strtoupper(substr($palabras[$i], 0, 1));
                        break;
                    }
                }
            }

            // Concatenar las iniciales para usarlas en el ID del equipo
            $prefijoEquipo = $categoriaInicial . $modalidadInicial;

            // Ahora puedes usar $prefijoEquipo para agregarlo al ID del equipo según tu lógica

            $proyecto = Proyectos::create($request->only([
                'nombre', 'categoria', 'modalidad_id', 'linea_investigacion_id', 'concurso_id','perfil_jurado'
            ]));

            $equipoId = Equipo::generarCodigoEquipo();
            $nuevaID = $prefijoEquipo . $equipoId;
            $equipo = Equipo::create([
                'id' => $nuevaID,
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
            $equipo = Equipo::with(['proyecto', 'participantes', 'proyecto.concurso', 'asesores'])
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
            'asesorMetodologico.tipo' => 'nullable|string', // Nuevo campo
            'asesorMetodologico.clavePresupuestal' => 'nullable|string|max:255', // Nuevo campo
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
                    'tipo_asesor' => $request->asesorMetodologico['tipo'], // Nuevo campo
                    'clave_presupuestal' => $request->asesorMetodologico['clavePresupuestal'], // Nuevo campo
                    'email' => $request->asesorMetodologico['correo'],
                    'telefono' => $request->asesorMetodologico['telefono'],
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


    // filepath: d:\proyecto\proyecto-pwa\app\Http\Controllers\ProyectosController.php
     public function descargarFormatos()
    {
        // Nombres de los archivos
        $archivos = [
            '2. FOAPA.docx',
            '3. FOCOMO.docx',
            '4. FOAS.docx',
            '5. FOACT.docx',
            '5A .ACTA DE FOACT.docx',
            '6. FOCP.docx',
            '7. FOHE.docx',
            '8. FOTAV.docx',
            '9. FOPAV.docx',
        ];

        // Ruta temporal para el archivo ZIP
        $zipPath = storage_path('app/public/formatos/formatos.zip');

        // Crear un archivo ZIP
       $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($archivos as $archivo) {
                $rutaArchivo = storage_path('app/public/formatos/' . $archivo);
                if (file_exists($rutaArchivo)) {
                    $zip->addFile($rutaArchivo, $archivo);
                }
            }
            $zip->close();
        }

        // Descargar el archivo ZIP
        if (file_exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'No se pudo crear el archivo ZIP.');
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

    public function subirDocumentosOpcionales(Request $request, $proyectoId)
    {
        $validator = Validator::make($request->all(), [
            'opcionales.*' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $proyecto = Proyectos::findOrFail($proyectoId);
            $equipoId = $proyecto->equipo_id;

            // Crear la carpeta con el ID del equipo si no existe
            $equipoFolder = storage_path("app/public/equipos/{$equipoId}/opcionales");
            if (!file_exists($equipoFolder)) {
                mkdir($equipoFolder, 0777, true);
            }

            // Guardar los documentos opcionales en la carpeta del equipo
            foreach ($request->file('opcionales') as $index => $documento) {
                $documento->move($equipoFolder, $documento->getClientOriginalName());
            }

            return response()->json(['success' => 'Documentos opcionales subidos exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al subir documentos opcionales: ' . $e->getMessage());
            return response()->json(['error' => 'Error al subir documentos opcionales.'], 500);
        }
    }

}