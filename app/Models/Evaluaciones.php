<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluaciones extends Model
{
    // Define the table name (optional if it matches the pluralized model name)
    protected $table = 'evaluaciones';

    // Define the fillable attributes
    protected $fillable = [
        'evaluador_id',
        'equipo_id',
        'estado',
    ];

    public function evaluador()
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}
