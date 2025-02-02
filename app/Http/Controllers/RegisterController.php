<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $rolesPermitidos = [
            'admin' => ['admin', 'vinculador', 'evaluador', 'participante', 'asesor'],
            'vinculador' => ['lider'],
            'lider' => ['participante'],
            'participante' => [],
            'evaluador' => [],
            'asesor' => [],
        ];

        $userRole = auth()->user()->rol;
        $rolesParaRegistrar = $rolesPermitidos[$userRole] ?? [];

        return Inertia::render('Auth/Register', [
            'rolesParaRegistrar' => $rolesParaRegistrar,
        ]);
    }
 /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);
        
        // 🔥 Dispara el evento para que se envíe el email de verificación
        event(new Registered($user));

        

        return redirect()->route('dashboard')->with('success', 'Usuario registrado correctamente.');
    }
}