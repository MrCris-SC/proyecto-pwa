<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipo extends Model
{
    // Indica que el primary key no se autoincrementa
    public $incrementing = false;

    // El tipo de la llave primaria es entero
    protected $keyType = 'integer';

    // Define los campos asignables
    protected $fillable = [
        'id', 
        'proyecto_id', 
        'concurso_id',
        'asesorescheck',
    ];

    // Método para generar un código único de 5 dígitos
    public static function generarCodigoEquipo()
    {
        do {
            // Genera un número aleatorio entre 1 y 99999 y lo convierte a cadena de 5 dígitos
            $codigo = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('id', (int)$codigo)->exists());

        return (int)$codigo;
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyectos::class, 'proyecto_id');
    }

    public function participantes()
    {
        // Usa el modelo correcto y asegúrate que el namespace es válido
        return $this->hasMany(Participantes::class, 'equipo_id', 'id');
    }
    public function asesores()
    {
        return $this->hasMany(Asesores::class, 'equipo_id');
    }

    public function concurso()
    {
        return $this->belongsTo(Concursos::class, 'concurso_id');
    }

    public function resultadoFinal()
    {
        return $this->hasOne(ResultadosFinales::class, 'equipo_id', 'id');
    }

    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }
}
