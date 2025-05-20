<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluaciones extends Model
{
    protected $table = 'evaluaciones';

    // Define the fillable attributes
    protected $fillable = [
        'evaluador_id',
        'equipo_id',
        'estado',
        'comentarios',
    ];

    public function evaluador()
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function puntajes()
    {
        return $this->hasMany(PuntajesEvaluacion::class, 'evaluacion_id');
    }

    public function proyecto()
    {
        return $this->hasOne(Proyectos::class);
    }
    
    public function concurso()
    {
        return $this->belongsTo(Concursos::class);
    }

}
