<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $rolesPermitidos = [
            'admin' => ['admin', 'vinculador', 'evaluador', 'participante', 'asesor'],
            'vinculador' => ['lider'],
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

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', 'string', 'in:admin,vinculador,participante,evaluador,asesor'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}