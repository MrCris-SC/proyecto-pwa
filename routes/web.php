<?php

use App\Http\Controllers\ConcursosFinales;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ConcursoController;
use App\Http\Controllers\ProyectosController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\EvaluadorController;
use App\Http\Controllers\EvaluacionesManualesController;
use App\Http\Controllers\EvaluacionesController;
use App\Http\Controllers\UsuarioGestionController;
use App\Models\Concursos;
use App\Http\Controllers\ResultadosFinalesController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified', 'role:evaluador'])->group(function () {
    /*Route::get('/evaluacion', [EvaluadorController::class, 'index'])->name('evaluacion.index');
    Route::get('/proyectos-asignados', [EvaluadorController::class, 'proyectosAsignados'])->name('proyectos.asignados');
    Route::get('/criterios', [EvaluadorController::class, 'criterios'])->name('criterios.index');
    Route::get('/reportes', [EvaluadorController::class, 'reportes'])->name('reportes.index');
    Route::get('/perfil', [EvaluadorController::class, 'perfil'])->name('perfil.index');*/
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if (!$user->perfil_completo) {
            return redirect()->route('perfil.completar');
        }

        if ($user->hasRole('evaluador') && !$user->has_completed_profiles) {
            return redirect()->route('perfil.select');
        }

        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/completar-perfil', [PerfilController::class, 'showForm'])->name('perfil.completar');
    Route::post('/completar-perfil', [PerfilController::class, 'guardarDatos'])->name('perfil.guardar');

    Route::get('/concursos/{concurso}/edit', [ConcursoController::class, 'edit'])->name('concursos.edit');
    Route::put('/concursos/{concurso}', [ConcursoController::class, 'update'])->name('concursos.update');
    Route::delete('/concursos/{concurso}', [ConcursoController::class, 'destroy'])->name('concursos.destroy');

    Route::get('/concursos', [ConcursoController::class, 'index'])->name('concursos.index');
    Route::post('/concursos', [ConcursoController::class, 'store'])->name('concursos.store');

    Route::get('/api/modalidades', [ProyectosController::class, 'index']);
    Route::post('/api/proyectos', [ProyectosController::class, 'store'])->name('proyectos.store');
    Route::post('/api/proyectos/{proyecto}/documentos', [ProyectosController::class, 'subirDocumento'])->name('proyectos.documentos.subir');
    Route::put('/api/proyectos/{proyecto}/documentos/{documento}', [ProyectosController::class, 'editarDocumento'])->name('proyectos.documentos.editar');
    Route::post('/api/proyectos/{proyecto}/opcionales', [ProyectosController::class, 'subirDocumentosOpcionales'])->name('proyectos.documentos.opcionales');
    Route::get('/api/estados/{estado}/planteles', [PerfilController::class, 'getPlanteles']);
    Route::get('/proyectos/{id}/pdf', [ProyectosController::class, 'pruebaVista'])->name('generar');
    Route::get('/generar-pdf', [ProyectosController::class, 'generarPDF'])->name('proyectos.pdf');

    Route::get('/gestion-de-proyectos', [ProyectosController::class, 'gestionProyectos'])->name('gestion.proyectos');
    Route::post('/concursos/{concurso}/inscribirse', [ProyectosController::class, 'inscribirse'])->name('concursos.inscribirse');
    
    Route::post('/registrar-asesor', [ProyectosController::class, 'registrarAsesor'])->name('registrar.asesor');

    // filepath: d:\proyecto\proyecto-pwa\routes\web.php
    Route::get('/descargar-formatos', [ProyectosController::class, 'descargarFormatos'])->name('descargar.formatos');

    // Ruta para descargar un formato individual
    //Route::get('/descargar-formatos/{archivo}', [ProyectosController::class, 'descargarFormato'])->name('descargar.formato');
    Route::get('/evaluacion', [EvaluadorController::class, 'index'])->name('evaluacion.index');
    Route::post('/evaluacion/{evaluacion}/guardar', [EvaluadorController::class, 'guardarEvaluacion'])->name('evaluacion.guardar');
    // Nueva ruta para enviar la evaluación final
    Route::post('/evaluacion/{evaluacion}/enviar-final', [EvaluadorController::class, 'enviarEvaluacionFinal'])->name('evaluacion.enviar-final');
    Route::get('/proyectos-asignados', [EvaluadorController::class, 'proyectosAsignados'])->name('proyectos.asignados');
    Route::get('/criterios', [EvaluadorController::class, 'criterios'])->name('criterios.index');
    Route::get('/reportes', [EvaluadorController::class, 'reportes'])->name('reportes.index');
    Route::get('/perfil', [EvaluadorController::class, 'perfil'])->name('perfil.index');

    // Mostrar formulario de registro
    Route::get('/registro', [ConcursoController::class, 'registroCriterios'])->name('criterios.registro');
    // Guardar criterios por tipo (I. Informe, II. Modalidad, III. Exposición)
    Route::post('/guardar-tipo-criterios', [ConcursoController::class, 'storeTipo'])->name('criterios.storeTipo');
    // Guardar todos los criterios de una modalidad
    Route::post('/guardar-modalidad-criterios', [ConcursoController::class, 'storeModalidad'])->name('criterios.storeModalidad');
    // Rutas antiguas (mantener temporalmente para compatibilidad)
    Route::post('/guardar-linea-criterios', [ConcursoController::class, 'storeLinea'])->name('criterios.storeLinea');
    Route::post('/guardar', [ConcursoController::class, 'guardarCriterios'])->name('criterios.store');

    Route::post('/concursos/{id}/cambiar-estado', [ConcursoController::class, 'cambiarEstado'])->name('concursos.cambiar.estado');
    // Nueva ruta para abrir el concurso
    Route::post('/concursos/{id}/abrir', [ConcursoController::class, 'abrirConcurso'])->name('concursos.abrir');
    Route::post('/concursos/{concurso}/evaluadores', [ConcursoController::class, 'registrarEvaluador'])->name('evaluadores.inscribir');

    Route::get('/equipos-registrados', [ConcursoController::class, 'verEquipos'])->name('equipos.registrados');
    Route::post('/concursos/{concursoId}/finalizar', [ConcursoController::class, 'finalizarConcurso'])->name('concursos.finalizar');

    Route::get('/select-profiles', [PerfilController::class, 'selectProfiles'])->name('perfil.select');
    Route::post('/save-profiles', [PerfilController::class, 'guardarPerfiles'])->name('perfil.save');

    // Ruta para mostrar los resultados de un concurso
    Route::get('/resultados', [ConcursoController::class, 'resultados'])->name('resultados.index');
    // Ruta para generar el reporte de resultados
    Route::get('/resultados/generar-reporte/{concursoId}/{equipoId}', [ConcursoController::class, 'generarReporte'])->name('resultados.generarReporte');

    Route::get('/concursos/{concurso}/evaluaciones-manuales-datos', [EvaluacionesManualesController::class, 'datos'])->name('concursos.evaluaciones.manuales.datos');

    // Nueva ruta para la vista de evaluadores asignados (para líderes)
    Route::get('/evaluadores-asignados', [EvaluacionesManualesController::class, 'vistaEvaluadoresAsignados'])->name('evaluadores.asignados');
    
    // ruta para la gestion de usuarios CRUD (admin)
    // Gestión de usuarios
    Route::get('/usuarios', [UsuarioGestionController::class, 'index'])->name('gestion.usuarios.index');
    Route::post('/usuarios', [UsuarioGestionController::class, 'store'])->name('gestion.usuarios.store');
    Route::put('/usuarios/{user}', [UsuarioGestionController::class, 'update'])->name('gestion.usuarios.update');
    Route::delete('/usuarios/{user}', [UsuarioGestionController::class, 'destroy'])->name('gestion.usuarios.destroy');
    
    // Eliminación específica de asesores
    Route::delete('/asesores/{asesor}', [UsuarioGestionController::class, 'destroyAsesor'])->name('asesores.destroy');
 
});


Route::get('/new-user', [RegisterController::class, 'showRegistrationForm'])->middleware(['auth'])->name('new.user');
Route::post('/register-store', [RegisterController::class, 'register'])->name('register.store');

Route::get('/api/estados', [PerfilController::class, 'getEstados']);
Route::get('/api/estados/{estado}/municipios', [PerfilController::class, 'getMunicipios']);

Route::get('/concursos/{id}/evaluaciones', [ConcursoController::class, 'getEvaluaciones'])->name('concursos.evaluaciones');
Route::get('/concursos/{id}/resumen-evaluaciones', [ConcursoController::class, 'obtenerResumenEvaluaciones'])->name('concursos.resumen.evaluaciones');

// Ruta para obtener los datos de un concurso
Route::get('/api/concursos/{id}', [ConcursoController::class, 'getConcurso'])->name('api.concursos.get');

// Ruta para obtener el podio
Route::get('/concursos/{id}/podio', [ConcursoController::class, 'obtenerPodio'])->name('concursos.podio');

Route::delete('/evaluaciones/{id}', [App\Http\Controllers\EvaluacionesController::class, 'destroy'])->name('evaluaciones.destroy');

// Ruta para obtener equipos de un concurso con su proyecto relacionado
Route::get('/concursos/{concurso}/equipos', function ($concursoId) {
    $equipos = \App\Models\Equipo::with([
        'proyecto.modalidad',
        'proyecto.lineaInvestigacion'
    ])->where('concurso_id', $concursoId)->get();
    return response()->json(['equipos' => $equipos]);
})->name('concursos.equipos');

// Ruta para obtener evaluadores (usuarios con rol evaluador)
Route::get('/concursos/{concurso}/evaluadores', function ($concursoId) {
    $evaluadores = \App\Models\User::where('rol', 'evaluador')->get();
    return response()->json(['evaluadores' => $evaluadores]);
})->name('concursos.evaluadores');

// Ruta para almacenar una nueva evaluación manualmente
Route::post('/evaluaciones', [EvaluacionesController::class, 'store'])->name('evaluaciones.store');

Route::get('/evaluador/equipo/{equipoId}/descargar-documentos', [EvaluadorController::class, 'descargarDocumentosEquipo'])
    ->name('evaluador.descargarDocumentosEquipo');

// Ruta para obtener equipos de un concurso con participantes y proyecto (para admin)
Route::get('/concursos-finales/{concurso}/equipos', [App\Http\Controllers\ConcursosFinales::class, 'equiposConParticipantes'])
    ->name('concursosFinales.equipos');

// Ruta para obtener resumen de evaluaciones de un proyecto (para admin)
Route::get('/concursos-finales/proyecto/{proyecto}/evaluaciones-resumen', [App\Http\Controllers\ConcursosFinales::class, 'evaluacionesResumenProyecto'])
    ->name('concursosFinales.evaluacionesResumen');

// Ruta para cambiar el estado de un proyecto (admin)
Route::post('/concursos-finales/proyecto/{proyecto}/cambiar-estado', [App\Http\Controllers\ConcursosFinales::class, 'cambiarEstadoProyecto'])
    ->name('concursosFinales.cambiarEstadoProyecto');

// Ruta para obtener participantes de un concurso (para admin)
Route::get('/concursos-finales/{concurso}/participantes', [\App\Http\Controllers\ConcursosFinales::class, 'participantesPorConcurso'])->name('concursosFinales.participantesPorConcurso');

// Ruta para descargar el reporte de equipos (admin)
Route::get('/concursos-finales/{concurso}/reporte-equipos', [\App\Http\Controllers\ConcursosFinales::class, 'descargarReporteEquipos'])->name('concursosFinales.descargarReporteEquipos');

// Ruta para generar el PDF de resultados finales
Route::get('/generar-podio-pdf', [ResultadosFinalesController::class, 'generarPDF']) ->name('resultados.pdf');

// Ruta para solicitar inscripción a un concurso
Route::post('/concursos/{concurso}/inscribir', [ProyectosController::class, 'solicitarInscripcion'])->name('concursos.inscribir');

// Nueva ruta para actualizar la clasificación de un usuario (admin)
Route::post('/usuarios/{user}/clasificacion', [ConcursosFinales::class, 'actualizarClasificacion'])
    ->name('usuarios.actualizarClasificacion');


require __DIR__.'/auth.php';