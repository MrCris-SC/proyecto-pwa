<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participantes extends Model
{
    protected $table = 'participantes';

    protected $fillable = [
        'nombre',
        'equipo_id',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
