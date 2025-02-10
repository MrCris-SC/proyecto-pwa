<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentacion;
use Illuminate\Support\Facades\Storage;

class DocumentacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file',
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        // Subir archivo a Firebase Storage
        $path = $request->file('archivo')->store('documentaciones', 'firebase');

        // Guardar la URL en la base de datos
        $documentacion = new Documentacion();
        $documentacion->nombre = $request->nombre;
        $documentacion->ruta = Storage::disk('firebase')->url($path);
        $documentacion->proyecto_id = $request->proyecto_id;
        $documentacion->save();

        return response()->json(['message' => 'Documentación subida exitosamente'], 201);
    }
}
