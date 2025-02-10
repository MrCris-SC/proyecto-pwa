<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ConcursoController;


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

    Route::get('/concursos', [ConcursoController::class, 'index'])->name('concursos.index');
    Route::post('/concursos', [ConcursoController::class, 'store'])->name('concursos.store');
});

Route::get('/new-user', [RegisterController::class, 'showRegistrationForm'])->middleware(['auth'])->name('new.user');
Route::post('/register-store', [RegisterController::class, 'register'])->name('register.store');

Route::get('/api/estados', [PerfilController::class, 'getEstados']);
Route::get('/api/estados/{estado}/municipios', [PerfilController::class, 'getMunicipios']);


require __DIR__.'/auth.php';
