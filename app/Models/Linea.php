<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    use HasFactory;

    protected $table = 'lineas_investigacion';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        
    ];

    public function proyectos()
    {
        return $this->hasMany(Proyectos::class, 'linea_investigacion_id');
    }
}
