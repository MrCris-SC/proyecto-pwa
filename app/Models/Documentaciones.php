<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentaciones extends Model
{
    use HasFactory;

    protected $table = 'documentaciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'ruta',
        'proyecto_id',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyectos::class, 'proyecto_id');
    }
}
