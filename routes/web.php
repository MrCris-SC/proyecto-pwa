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
use App\Models\Concursos;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
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


   // Rutas para editar y eliminar concursos
    Route::get('/concursos/{concurso}/edit', [ConcursoController::class, 'edit'])->name('concursos.edit');
    Route::put('/concursos/{concurso}', [ConcursoController::class, 'update'])->name('concursos.update');
    Route::delete('/concursos/{concurso}', [ConcursoController::class, 'destroy'])->name('concursos.destroy');
    
    Route::get('/concursos', [ConcursoController::class, 'index'])->name('concursos.index');
    Route::post('/concursos', [ConcursoController::class, 'store'])->name('concursos.store');

    

    Route::get('/api/modalidades', [ProyectosController::class, 'index']);
    Route::post('/api/proyectos', [ProyectosController::class, 'store']) ->name('proyectos.store');
    Route::post('/api/proyectos/{proyecto}/equipo', [ProyectosController::class, 'storeEquipo']);
    Route::get('/api/estados/{estado}/planteles', [PerfilController::class, 'getPlanteles']);
    Route::get('/proyectos/{id}/pdf', [ProyectosController::class, 'generarPDF'])->name('proyectos.pdf');

    Route::post('/concursos/{concurso}/inscribirse', [ConcursoController::class, 'inscribirse'])->name('concursos.inscribirse');
});

Route::get('/new-user', [RegisterController::class, 'showRegistrationForm'])->middleware(['auth'])->name('new.user');
Route::post('/register-store', [RegisterController::class, 'register'])->name('register.store');



Route::get('/api/estados', [PerfilController::class, 'getEstados']);
Route::get('/api/estados/{estado}/municipios', [PerfilController::class, 'getMunicipios']);


require __DIR__.'/auth.php';
