<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyectos extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'categoria',
        'modalidad_id',
        'linea_investigacion_id',
        'concurso_id'
    ];

    public function modalidad()
    {
        return $this->belongsTo(Modalidades::class);
    }

    public function lineaInvestigacion()
    {
        return $this->belongsTo(Linea::class);
    }

    public function concurso()
    {
        return $this->belongsTo(Concursos::class);
    }
}

