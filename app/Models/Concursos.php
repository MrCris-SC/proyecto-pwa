<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concursos extends Model
{
    use HasFactory;

    protected $table = 'concursos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_apertura',
        'fecha_terminacion',
        'status',
        'fase',
        'estado',
        'plantel_id',
    ];

    protected $appends = ['estado_nombre', 'plantel_nombre'];

    public function plantel()
    {
        return $this->belongsTo(Planteles::class, 'plantel_id');
    }

    public function estadoRelation()
    {
        return $this->belongsTo(Estados::class, 'estado', 'idestado');
    }

    public function evaluadores()
    {
        return $this->belongsToMany(User::class, 'concurso_evaluador', 'concurso_id', 'evaluador_id')
                    ->where('rol', 'evaluador');
    }

    public function criteriosEvaluacion()
    {
        return $this->hasMany(CriteriosEvaluacion::class, 'concurso_id');
    }

    public function evaluaciones()
    {
        return $this->hasManyThrough(
            Evaluaciones::class,
            Equipo::class,
            'concurso_id', // Foreign key on the equipos table
            'equipo_id',   // Foreign key on the evaluaciones table
            'id',          // Local key on the concursos table
            'id'           // Local key on the equipos table
        );
    }

// En App\Models\Concursos.php

public function equiposConLideres()
{
    return $this->hasMany(Equipo::class, 'concurso_id')->with('lider');
}

public function getLideresAttribute()
{
    if (!array_key_exists('equiposConLideres', $this->relations)) {
        $this->load('equiposConLideres');
    }

    return $this->equiposConLideres->pluck('lider')->filter();
}

public function proyectosConAsesores()
{
    return $this->hasMany(Proyectos::class, 'concurso_id')->with('asesores');
}

public function getAsesoresAttribute()
{
    if (!array_key_exists('proyectosConAsesores', $this->relations)) {
        $this->load('proyectosConAsesores');
    }

    return $this->proyectosConAsesores->flatMap->asesores;
}

public function todosLosUsuarios()
{
    // Obtener usuarios evaluadores directamente asociados al concurso
    $evaluadores = $this->evaluadores;
    
    // Obtener líderes de equipos (asumiendo que Equipo tiene relación con User)
    $lideres = $this->equiposConLideres->map->lider;
    
    // Obtener asesores a través de proyectos
    $asesores = $this->proyectosConAsesores->map->asesor;
    
    // Obtener administradores (todos los users con rol admin)
    $admins = User::where('rol', 'admin')->get();
    
    // Combinar todas las colecciones
    return $evaluadores
        ->merge($lideres)
        ->merge($asesores)
        ->merge($admins)
        ->unique('id');
}

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'concurso_id');
    }

    public function getEstadoNombreAttribute()
    {
        if ($this->fase === 'estatal') {
            return $this->estadoRelation->nombre ?? 'No especificado';
        }
        return null;
    }

    public function getPlantelNombreAttribute()
    {
        return $this->plantel->nombre_corto ?? null;
    }
}