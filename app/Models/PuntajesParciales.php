<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntajesParciales extends Model
{
    protected $table = 'puntajes_parciales';

    protected $fillable = [
        'evaluacion_id',
        'equipo_id',
        'concurso_id',
        'puntaje_total',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluaciones::class, 'evaluacion_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function concurso()
    {
        return $this->belongsTo(Concursos::class, 'concurso_id');
    }
}
