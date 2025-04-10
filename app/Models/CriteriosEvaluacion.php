<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriosEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'criterios_evaluacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'puntaje_maximo',
        'concurso_id',
        'linea_investigacion_id', // Nuevo campo
    ];

    // Relación con Concursos
    public function concurso()
    {
        return $this->belongsTo(Concursos::class, 'concurso_id');
    }


    // Relación con Puntajes
    public function puntajes()
    {
        return $this->hasMany(PuntajesEvaluacion::class, 'criterio_id');
    }

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'linea_investigacion_id');
    }
}