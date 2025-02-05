<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    use HasFactory;

    protected $table = 'municipios'; // Nombre de la tabla
    protected $primaryKey = 'idmunicipio'; // Llave primaria
    
    protected $fillable = [
        'nombre',
        'idestado',
        'clave',
        'sigla',
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'idestado');
    }
}
