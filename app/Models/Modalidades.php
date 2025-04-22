<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modalidades extends Model
{
    use HasFactory;

    protected $table = 'modalidades';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'tipo',
    ];


    // Relación con CriteriosEvaluacion
    public function criterios()
    {
        return $this->hasMany(CriteriosEvaluacion::class, 'modalidad_id');
    }


}
