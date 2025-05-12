<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadosFinales extends Model
{
    protected $table = 'resultados_finales';

    protected $fillable = [
        'equipo_id',
        'concurso_id',
        'promedio_final',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function concurso()
    {
        return $this->belongsTo(Concursos::class, 'concurso_id');
    }
}
