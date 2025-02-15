<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Concursos;

class ConcursoController extends Controller
{
    /**
     * Muestra la página de concursos.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        $concursos = Concursos::all();

        // Si el usuario es líder y ya está inscrito en un concurso, filtrar la lista
        if ($user->rol === 'lider' && $user->concurso_registrado_id) {
            $concursos = $concursos->where('id', $user->concurso_registrado_id);
        }

        return Inertia::render('ConcursosLayouts/Concursos', ['concursos' => $concursos]);
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
            'fecha_terminacion' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Agregar el status del concurso
        $request['status'] = 'abierto';
        $request['fecha_apertura'] = now()->toDateString();

        // Crear el concurso
        $concurso = Concursos::create($request->all());

        return redirect()->route('concursos.index')->with('success', 'Concurso creado exitosamente.');
    }

    /**
     * Muestra la página de edición de un concurso.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit(Concursos $concurso)
    {
        // Al consultar el concurso, Inertia pasará el objeto completo
        return Inertia::render('ConcursosLayouts/EditarConcurso', ['concurso' => $concurso]);
    }

    /**
     * Actualiza un concurso existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_terminacion' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Buscar el concurso por ID
        $concurso = Concursos::findOrFail($id);

        // Actualizar los datos del concurso
        $concurso->update($request->all());

        // Redirigir con un mensaje de éxito
        return redirect()->route('concursos.index')->with('success', 'Concurso actualizado exitosamente.');
    }

    /**
     * Elimina un concurso existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Buscar el concurso por ID
        $concurso = Concursos::findOrFail($id);

        // Eliminar el concurso
        $concurso->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('concursos.index')->with('success', 'Concurso eliminado exitosamente.');
    }
}