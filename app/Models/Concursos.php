<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concursos extends Model
{
    use HasFactory;

    protected $table = 'concursos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_apertura',
        'fecha_terminacion',
        'status',
        'fase',
        'plantel_id',
    ];

    

    public function plantel()
    {
        return $this->belongsTo(Planteles::class, 'plantel_id');
    }
}
