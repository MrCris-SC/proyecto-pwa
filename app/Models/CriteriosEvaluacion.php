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
        'puntaje_maximo',
        'concurso_id',
        'modalidad_id',
        'tipo_criterio'
    ];

    public function concurso()
    {
        return $this->belongsTo(Concursos::class, 'concurso_id');
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidades::class, 'modalidad_id');
    }

    public function puntajes()
    {
        return $this->hasMany(PuntajesEvaluacion::class, 'criterio_id');
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_criterio', $tipo);
    }

    public function scopePorModalidad($query, $modalidadId)
    {
        return $query->where('modalidad_id', $modalidadId);
    }
}