<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesores extends Model
{
    use HasFactory;
    
    protected $table = 'asesores';

    // Campos que se pueden asignar masivamente
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

    // Relación con la tabla equipos
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}