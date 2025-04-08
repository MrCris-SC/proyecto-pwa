<?php

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
use App\Http\Controllers\EvaluadorController; // Nuevo controlador
use App\Models\Concursos;

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
    Route::get('/proyectos-asignados', [EvaluadorController::class, 'proyectosAsignados'])->name('proyectos.asignados');
    Route::get('/criterios', [EvaluadorController::class, 'criterios'])->name('criterios.index');
    Route::get('/reportes', [EvaluadorController::class, 'reportes'])->name('reportes.index');
    Route::get('/perfil', [EvaluadorController::class, 'perfil'])->name('perfil.index');

    Route::get('/registro', [ConcursoController::class, 'registroCriterios'])->name('criterios.registro');
            
    Route::post('/guardar', [ConcursoController::class, 'guardarCriterios'])->name('criterios.store');

    Route::post('/concursos/{id}/cambiar-estado', [ConcursoController::class, 'cambiarEstado'])->name('concursos.cambiar.estado');
    Route::post('/concursos/{concurso}/evaluadores', [ConcursoController::class, 'registrarEvaluador'])->name('evaluadores.inscribir');
});

Route::get('/new-user', [RegisterController::class, 'showRegistrationForm'])->middleware(['auth'])->name('new.user');
Route::post('/register-store', [RegisterController::class, 'register'])->name('register.store');

Route::get('/api/estados', [PerfilController::class, 'getEstados']);
Route::get('/api/estados/{estado}/municipios', [PerfilController::class, 'getMunicipios']);

require __DIR__.'/auth.php';