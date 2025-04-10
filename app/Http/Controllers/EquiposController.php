public function index()
{
    $equipos = Equipo::all(); // Obtén los equipos desde la base de datos
    return Inertia::render('ConcursosLayouts/EquiposRegistrados', [
        'equipos' => $equipos,
    ]);
}
