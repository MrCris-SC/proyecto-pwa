<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estados extends Model
{
    use HasFactory;

    protected $table = 'estados'; // Nombre de la tabla
    protected $primaryKey = 'idestado'; // Llave primaria
    protected $fillable = [
        'clave',
        'nombre',
        'abreviatura',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipios::class, 'idestado');
    }
}
