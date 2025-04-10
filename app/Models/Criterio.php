<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Criterio extends Model
{
    protected $table = 'criterios_evaluacion'; // Mantenemos el nombre de tabla original
    
    protected $fillable = [
        'concurso_id',
        'nombre',
        'puntaje_maximo',
        'linea_investigacion_id' // Asegúrate de incluir este campo
    ];

    public function concurso(): BelongsTo
    {
        return $this->belongsTo(Concursos::class);
    }

    public function linea(): BelongsTo
    {
        return $this->belongsTo(Linea::class, 'linea_investigacion_id');
    }
}