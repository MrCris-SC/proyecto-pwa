<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participantes extends Model
{
    protected $table = 'participantes'; // Laravel lo deduce automáticamente, pero puedes dejarlo

    protected $fillable = [
        'nombre',
        'equipo_id',
        'correo', 
        'telefono',
        'direccion',
        'proyecto_id',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyectos::class);
    }
}
