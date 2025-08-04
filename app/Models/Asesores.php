<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesores extends Model
{
    use HasFactory;
    
    protected $table = 'asesores';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'tipo_asesor',
        'clave_presupuestal',
        'nivel_academico',
        'perfiles_jurado',
        'equipo_id',
        'asesor_origen', 
    ];

    // Relación con el equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    // Relación con proyecto a través del equipo (opcional)
    public function proyectoDelEquipo()
    {
        return $this->hasOneThrough(
            Proyectos::class,
            Equipo::class,
            'id', // FK en equipos
            'equipo_id', // FK en proyectos
            'equipo_id', // Local key en asesores
            'id' // Local key en equipos
        );
    }

}