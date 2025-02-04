<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        
        if ($user->perfil_completo) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/CompleteProfile', [
            'user' => $user,
        ]);
    }

    public function completarPerfil()
    {
        return Inertia::render('Auth/CompletarPerfil');
    }

    public function guardarDatos(Request $request)
    {
        $request->validate([
            'genero' => 'required|string',
            'telefono' => 'required|string',
            'direccion' => 'required|string',
            'grado_estudio' => 'required|string',
        ]);

        $user = auth()->user();
        $user->update([
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'grado_estudio' => $request->grado_estudio,
            'perfil_completo' => true, // Marcar el perfil como completo
        ]);

        session()->forget('completar_perfil');

        return redirect()->route('dashboard')->with('success', 'Perfil completado correctamente.');
    }
}
