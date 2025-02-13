<?php

namespace App\Http\Controllers;

use App\Models\Participantes;
use Illuminate\Http\Request;
use App\Models\Equipo;


class EquipoController extends Controller
{
    public function store(Request $request, $proyecto_id)
    {
        $request->validate([
            'integrantes' => 'required|array',
            'integrantes.*.nombre' => 'required|string|max:255'
        ]);

        // Crear el equipo asociado al proyecto
        $equipo = Equipo::create(['proyecto_id' => $proyecto_id]);

        // Crear los integrantes del equipo
        foreach ($request->integrantes as $integranteData) {
            Participantes::create([
                'equipo_id' => $equipo->id,
                'nombre' => $integranteData['nombre']
            ]);
        }

        return response()->json(['success' => true]);
    }
}
