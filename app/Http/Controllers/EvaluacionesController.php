<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluaciones;

class EvaluacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'evaluador_id' => 'required|exists:users,id',
            'equipo_id' => 'required|exists:equipos,id',
            'estado' => 'required|string',
        ]);

        $evaluacion = Evaluaciones::create([
            'evaluador_id' => $validated['evaluador_id'],
            'equipo_id' => $validated['equipo_id'],
            'estado' => $validated['estado'],
            'comentarios' => $request->input('comentarios', null),
        ]);

        return response()->json([
            'success' => true,
            'evaluacion' => $evaluacion,
            'message' => 'Evaluación creada correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $evaluacion = Evaluaciones::find($id);
        if (!$evaluacion) {
            return response()->json(['success' => false, 'message' => 'Evaluación no encontrada.'], 404);
        }

        $evaluacion->delete();

        return response()->json(['success' => true, 'message' => 'Evaluación eliminada correctamente.']);
    }
}
