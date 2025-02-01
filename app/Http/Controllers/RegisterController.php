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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|string|max:255', // Añadido campo rol
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol, // Añadido campo rol
        ]);

        auth()->login($user);

        return redirect()->route('home');
    }
}