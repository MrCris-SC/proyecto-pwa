<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PuntajesEvaluacion extends Model
{

    protected $table = 'puntajes_evaluacion';

    protected $fillable = [
        'evaluacion_id',
        'criterio_id',
        'puntaje_obtenido',
        'comentario',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluaciones::class, 'evaluacion_id');
    }

    public function criterio()
    {
        return $this->belongsTo(CriteriosEvaluacion::class, 'criterio_id');
    }
}