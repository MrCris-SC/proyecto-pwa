<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ConcursoController extends Controller
{
    /**
     * Muestra la página de concursos.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Concursos');
    }

    /**
     * Almacena un nuevo concurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validar y almacenar el concurso
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Crear el concurso (aquí puedes agregar la lógica para almacenar el concurso en la base de datos)

        return redirect()->route('Concursos.index')->with('success', 'Concurso creado exitosamente.');
    }
}