<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Concursos;
use App\Models\Estados;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Criterio;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ConcursoController extends Controller
{
    use AuthorizesRequests;
    /**
     * Muestra la página de concursos
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        
        // Cargar concursos con relaciones optimizadas
        $concursos = Concursos::with([
            'plantel',
            'estadoRelation' => function($query) {
                $query->select('idestado', 'nombre');
            }
        ])->get();

        // Procesar los datos para incluir nombres de estado y plantel
        $concursos->each(function ($concurso) {
            // Para concursos estatales
            if ($concurso->fase === 'estatal') {
                $concurso->estado_nombre = $concurso->estadoRelation->nombre ?? 'No especificado';
            }
            
            // Para concursos locales (opcional)
            if ($concurso->fase === 'local' && $concurso->plantel) {
                $concurso->plantel_nombre = $concurso->plantel->nombre_corto ?? null;
            }
        });

        // Filtrar para líderes si es necesario
        if ($user->rol === 'lider') {
            if ($user->concurso_registrado_id) {
                $concursos = $concursos->where('id', $user->concurso_registrado_id);
            } else {
                $concursos = $concursos->filter(function ($concurso) use ($user) {
                    return $concurso->plantel && $concurso->plantel->estado_id === $user->estado_id;
                });
            }
        }

        return Inertia::render('ConcursosLayouts/Concursos', [
            'concursos' => $concursos,
            'inscrito' => (bool)$user->concurso_registrado_id,
            'flash' => ['success' => request()->query('success', '')]
        ]);
    }

    /**
     * Almacena un nuevo concurso
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_terminacion' => 'required|date|after_or_equal:fecha_inicio',
            'fase' => 'required|string|in:local,estatal,nacional',
            'estado' => 'required_if:fase,estatal|nullable|integer|exists:estados,idestado',
            'plantel_id' => 'required_if:fase,local|nullable|integer|exists:planteles,id_plantel',
        ]);

        // Crear el concurso con los datos validados
        $concurso = Concursos::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_terminacion' => $validated['fecha_terminacion'],
            'fase' => $validated['fase'],
            'estado' => $validated['estado'] ?? null,
            'plantel_id' => $validated['plantel_id'] ?? null,
            'status' => 'abierto',
            'fecha_apertura' => now()->toDateString(),
        ]);

        return redirect()->route('concursos.index')->with('success', 'Concurso creado exitosamente.');
    }

    /**
     * Muestra el formulario de edición
     *
     * @param  \App\Models\Concursos  $concurso
     * @return \Inertia\Response
     */
    public function edit(Concursos $concurso)
    {
        return Inertia::render('ConcursosLayouts/EditarConcurso', [
            'concurso' => $concurso->load(['estadoRelation', 'plantel'])
        ]);
    }

    /**
     * Actualiza un concurso existente
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_terminacion' => 'required|date|after_or_equal:fecha_inicio',
            'fase' => 'required|string|in:local,estatal,nacional',
            'estado' => 'required_if:fase,estatal|nullable|integer|exists:estados,idestado',
            'plantel_id' => 'required_if:fase,local|nullable|integer|exists:planteles,id_plantel',
        ]);

        $concurso = Concursos::findOrFail($id);
        $concurso->update($validated);

        return redirect()->route('concursos.index')->with('success', 'Concurso actualizado exitosamente.');
    }

    /**
     * Elimina un concurso
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $concurso = Concursos::findOrFail($id);
        $concurso->delete();

        return redirect()->route('concursos.index')->with('success', 'Concurso eliminado exitosamente.');
    }

    /**
     * Inscribe al usuario en un concurso
     *
     * @param  int  $concursoId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inscribirse($concursoId)
    {
        $user = auth()->user();

        if ($user->concurso_registrado_id) {
            return redirect()->route('gestion-de-proyectos')->with('error', 'Ya estás inscrito en un concurso.');
        }

        $concurso = Concursos::find($concursoId);
        if (!$concurso) {
            return redirect()->route('concursos.index')->with('error', 'El concurso no existe.');
        }

        $user->concurso_registrado_id = $concursoId;
        $user->save();

        return redirect()->route('gestion-de-proyectos')->with('success', 'Inscripción exitosa.');
    }

    /**
     * Método de diagnóstico para verificar estados (temporal)
     */
    public function diagnosticarEstados()
    {
        $concursosProblema = Concursos::where('fase', 'estatal')
            ->whereNotNull('estado')
            ->whereDoesntHave('estadoRelation')
            ->get();

        if ($concursosProblema->isNotEmpty()) {
            $resultado = "Los siguientes concursos tienen IDs de estado inválidos:\n";
            foreach ($concursosProblema as $concurso) {
                $resultado .= "Concurso ID: {$concurso->id}, Estado ID: {$concurso->estado}\n";
            }
            return response($resultado);
        }

        return response("Todos los concursos estatales tienen estados válidos.");
    }

    public function registroCriterios()
    {
        $concursos = Concursos::all(); // Obtén los concursos
        $criterios = Criterio::all(); // Obtén los criterios existentes

        return Inertia::render('ConcursosLayouts/Concursos', [
            'concursos' => $concursos,
            'criterios' => $criterios,
        ]);
    }

    public function guardarCriterios(Request $request)
    {
        $validated = $request->validate([
            'criterios' => 'required|array|min:1',
            'criterios.*.nombre' => 'required|string|max:255',
            'criterios.*.puntaje_maximo' => 'required|numeric|min:1',
        ]);

        // Elimina todos los criterios existentes (o usa update si prefieres conservar IDs)
        Criterio::truncate();
        
        // Crea nuevos criterios
        foreach ($validated['criterios'] as $criterioData) {
            Criterio::create([
                'nombre' => $criterioData['nombre'],
                'puntaje_maximo' => $criterioData['puntaje_maximo']
            ]);
        }

        return redirect()->back()->with('success', 'Criterios guardados exitosamente.');
    }
}