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
        'puntaje_maximo'
    ];

    public function concurso(): BelongsTo
    {
        return $this->belongsTo(Concursos::class);
    }
}