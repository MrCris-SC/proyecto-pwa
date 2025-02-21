<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planteles extends Model
{
    use HasFactory;

    protected $table = 'planteles';
    protected $primaryKey = 'id_plantel';
    public $incrementing = true;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'nombre_completo',
        'nombre_corto',
        'direccion',
        'telefono',
        'correo',
        'estado_id',
    ];

    protected $casts = [
        'id_plantel' => 'integer', // Correct the cast type
        'estado_id' => 'integer',
    ];

    public function estado()
{
    return $this->belongsTo(Estados::class, 'estado_id');
}
}
