<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Asesores;
use App\Models\Concursos;
use App\Models\Proyectos; // Asegúrate de importar el modelo Proyecto
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class UsuarioGestionController extends Controller
{
    /**
     * Muestra la lista combinada de usuarios y asesores
     */

public function index(Request $request)
{
    if (auth()->user()->rol !== 'admin') {
        abort(403, 'Acceso no autorizado');
    }

    $concursos = Concursos::select(['id', 'nombre'])->orderBy('nombre')->get();
    $selectedConcurso = $request->input('concurso_id');
    $todosUsuarios = collect();

    if ($selectedConcurso) {
        // 1. Obtener evaluadores (versión simplificada sin whereHas)
        $evaluadores = User::where('rol', 'evaluador')
            ->where('concurso_registrado_id', $selectedConcurso)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => 'evaluador',
                    'tipo_asesor' => null
                ];
            });

        // 2. Obtener líderes
        $lideres = User::where('rol', 'lider')
            ->where('concurso_registrado_id', $selectedConcurso)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => 'lider',
                    'tipo_asesor' => null
                ];
            });

        // 3. Obtener asesores (versión corregida con whereHas)
        $asesores = Asesores::whereHas('equipo', function($query) use ($selectedConcurso) {
                $query->where('concurso_id', $selectedConcurso);
            })
            ->get()
            ->map(function($asesor) {
                return [
                    'id' => $asesor->id,
                    'name' => $asesor->nombre,
                    'email' => $asesor->email,
                    'rol' => 'asesor',
                    'tipo_asesor' => $asesor->tipo_asesor
                ];
            });

        // 4. Administradores
        $admins = User::where('rol', 'admin')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => 'admin',
                    'tipo_asesor' => null
                ];
            });

        // Combinar todos
        $todosUsuarios = collect()
            ->merge($evaluadores)
            ->merge($lideres)
            ->merge($asesores)
            ->merge($admins)
            ->unique('id');
    }

    return Inertia::render('ConcursosLayouts/GestionUsuarios', [
        'users' => $todosUsuarios,
        'concursos' => $concursos,
        'filters' => $request->only(['concurso_id']),
        'userRole' => auth()->user()->rol,
        'status' => session('status')
    ]);
}

    /**
     * Almacena un nuevo usuario normal
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'rol' => 'required|in:evaluador,lider' // No permitir crear admins desde aquí
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol
        ]);

        return redirect()->route('gestion.usuarios.index')
            ->with('status', 'Usuario creado correctamente');
    }

    /**
     * Actualiza un usuario existente
     */
    public function update(Request $request, User $user)
    {
        // No permitir modificar otros admins a menos que sea superadmin
        if ($user->rol === 'admin' && auth()->user()->id !== $user->id) {
            abort(403, 'No puedes modificar este usuario');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'rol' => 'required|in:admin,evaluador,lider' // Excluye asesor
        ]);

        $updateData = $request->only(['name', 'email', 'rol']);

        if ($request->password) {
            $request->validate([
                'password' => [Rules\Password::defaults()],
            ]);
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('gestion.usuarios.index')
            ->with('status', 'Usuario actualizado correctamente');
    }

    /**
     * Elimina un usuario normal
     */
    public function destroy(User $user)
    {
        // No permitir eliminarse a sí mismo
        if (auth()->user()->id === $user->id) {
            abort(403, 'No puedes eliminarte a ti mismo');
        }

        // No permitir eliminar admins
        if ($user->rol === 'admin') {
            abort(403, 'No se pueden eliminar cuentas de administrador');
        }

        $user->delete();

        return redirect()->route('gestion.usuarios.index')
            ->with('status', 'Usuario eliminado correctamente');
    }

    /**
     * Elimina un asesor
     */
    public function destroyAsesor(Asesores $asesor)
    {
        $asesor->delete();

         return redirect()->route('gestion.usuarios.index')
            ->with('status', 'Usuario eliminado correctamente');
    }
}