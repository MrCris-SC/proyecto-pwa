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

    public function register(Request $request)
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

        event(new Registered($user));

        

        return redirect(route('dashboard', absolute: false));
    }
}