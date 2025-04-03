<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class EvaluadorController extends Controller
{
    public function index()
    {
        return Inertia::render('ConcursosLayouts/Evaluacion');
    }

    public function proyectosAsignados()
    {
        return Inertia::render('ConcursosLayouts/ProyectosAsignados');
    }

    public function criterios()
    {
        return Inertia::render('ConcursosLayouts/Criterios');
    }

    public function reportes()
    {
        return Inertia::render('ConcursosLayouts/Reportes');
    }

    public function perfil()
    {
        return Inertia::render('ConcursosLayouts/Perfil');
    }
}