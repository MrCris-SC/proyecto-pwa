<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Planteles;
use App\Models\Evaluadores;

class PerfilController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();

        if ($user->perfil_completo) {
            // Check if the user is an evaluator and hasn't completed profiles
            if ($user->rol === 'evaluador' && !$user->has_completed_profiles) {
                return redirect()->route('perfil.select');
            }

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
           // \Log::info('Fetching planteles for estado_id: ' . $estadoId);

            $planteles = Planteles::where('estado_id', $estadoId)->get();

            // Log the retrieved planteles
            //\Log::info('Retrieved planteles: ' . $planteles->toJson());

            return response()->json($planteles);
        } catch (\Exception $e) {
            // Log any errors
            //\Log::error('Error fetching planteles: ' . $e->getMessage());

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

    public function guardarPerfiles(Request $request)
    {
        // Permitir string o array para compatibilidad
        $perfil = $request->perfil;
        if (is_string($perfil)) {
            $perfil = [$perfil];
        }
        $request->merge(['perfil' => $perfil]);
        $request->validate([
            'perfil' => 'required|array|max:3', // Maximum of 3 profiles
            'perfil.*' => 'string',
        ]);

        $user = Auth::user();
        $evaluador = Evaluadores::firstOrCreate(['userID' => $user->id]);
        $evaluador->update(['perfil' => implode(',', $request->perfil)]);


        $user->update(['has_completed_profiles' => true]);

        return redirect()->route('dashboard')->with('success', 'Perfiles guardados correctamente.');
    }

    public function selectProfiles()
    {
        $user = Auth::user();

        // Removed is_evaluador check
        return Inertia::render('Auth/SelectProfiles', [
            'perfilesDisponibles' => $this->getPerfilesDisponibles(),
        ]);
    }

    private function getPerfilesDisponibles()
    {
        $perfiles = [
            "Ingeniera/o ambiental",
            "Ingeniera/o civil",
            "Ingeniera/o en agrícola",
            "Ingeniera/o en alimentos",
            "Ingeniera/o en análisis de sistemas",
            "Ingeniera/o en bioingeniería",
            "Ingeniera/o en bioquímico",
            "Ingeniera/o en ciencias de la computación",
            "Ingeniera/o en desarrollo de software",
            "Ingeniera/o en energías renovables",
            "Ingeniera/o en electrónica",
            "Ingeniera/o en logística",
            "Ingeniera/o en mecánico",
            "Ingeniera/o en mecatrónica",
            "Ingeniera/o en química",
            "Ingeniera/o en sistemas computacionales",
            "Ingeniera/o en tics",
            "Ingeniera/o industrial",
            "Ingeniería forestal",
            "Ingeniería y gestión de proyectos",
            "Enfermera/o",
            "Médica/o",
            "Psicóloga/o",
            "Sociólogo",
            "Trabajador/a social",
            "Educación",
            "Educación especial e inclusión",
            "Profesor/a",
            "Administrador de empresas",
            "Administrador de empresas turísticas",
            "Administrativa/o",
            "Administrativa/o comercial",
            "Administrativa/o de gestión y personal",
            "Agrónoma/o",
            "Análisis de sistemas",
            "Analista de mercados",
            "Antropóloga/o",
            "Arquitecta/o",
            "Biología marina",
            "Biólogo",
            "Biotecnología",
            "Ciencias de la computación",
            "Ciencias de la educación",
            "Comunicación y medios de información",
            "Consultor/a empresarial",
            "Contable",
            "Contaduría",
            "Desarrollo de software",
            "Desarrollo sustentable",
            "Dietista",
            "Diseñador/a gráfico",
            "Economista",
            "Educador/a infantil",
            "Educador/a primaria",
            "Electricista",
            "Eléctrico",
            "Emprendedor",
            "Emprendedor de negocios",
            "Enseñanza interactiva",
            "Especialista en el área de aplicación",
            "Farmacéutica",
            "Fisioterapeuta",
            "Gastronomía",
            "Gerente de empresas",
            "Gestión empresarial",
            "Informática/o",
            "Ingeniero químico en alimentos",
            "Innovación educativa",
            "Innovación tecnológica",
            "Innovación y desarrollo empresarial",
            "Licenciatura en informática",
            "Literatura",
            "Matemática/o",
            "Medicina general",
            "Mercadólogo",
            "Nutricionista",
            "Pedagoga/o",
            "Preparador/a físico/a deportivo/a",
            "Profesor de universidad",
            "Psicopedagogo",
            "Psiquiatra",
            "Publicitaria/o",
            "Recursos humanos",
            "Sistemas computacionales",
            "Traductor/a",
            "Turismo",
            "Veterinaria/o",
            "Otro",
        ];

        return $perfiles;
    }
}
