<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriosEvaluacion extends Model
{

    protected $table = 'criterios_evaluacion';

    protected $fillable = [
        'nombre',
        'puntaje_maximo',
    ];
}