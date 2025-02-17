<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Planteles;

class PerfilController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        
        if ($user->perfil_completo) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/CompleteProfile', [
            'user' => $user,
        ]);
    }

    public function getPlanteles($estadoId)
    {
        try {
            // Log the estadoId to ensure it's being received correctly
            \Log::info('Fetching planteles for estado_id: ' . $estadoId);

            $planteles = Planteles::where('estado_id', $estadoId)->get();

            // Log the retrieved planteles
            \Log::info('Retrieved planteles: ' . $planteles->toJson());

            return response()->json($planteles);
        } catch (\Exception $e) {
            // Log any errors
            \Log::error('Error fetching planteles: ' . $e->getMessage());

            return response()->json(['error' => 'Error fetching planteles'], 500);
        }
    }

    public function completarPerfil()
    {
        return Inertia::render('Auth/CompletarPerfil');
    }

    public function guardarDatos(Request $request)
    {
        $request->validate([
            'genero' => 'required|string',
            'telefono' => 'required|string',
            'direccion' => 'required|string',
            'grado_estudio' => 'required|string',
            'estado_id' => 'required|exists:estados,idestado',
            'municipio_id' => 'required|exists:municipios,idmunicipio',
            'plantel_id' => 'required|exists:planteles,id_plantel',
        ]);

        $user = auth()->user();
        $user->update([
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'grado_estudio' => $request->grado_estudio,
            'estado_id' => $request->estado_id,
            'municipio_id' => $request->municipio_id,
            'plantel_id' => $request->plantel_id,
            'perfil_completo' => true, // Marcar el perfil como completo
        ]);

        session()->forget('completar_perfil');

        return redirect()->route('dashboard')->with('success', 'Perfil completado correctamente.');
    }

    public function getEstados()
    {
        return response()->json(Estados::all());
    }

    public function getMunicipios($estado_id)
    {
        return response()->json(Municipios::where('idestado', $estado_id)->get());
    }
}
