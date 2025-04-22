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
        'estado',
        'plantel_id',
    ];

    protected $appends = ['estado_nombre', 'plantel_nombre'];

    public function plantel()
    {
        return $this->belongsTo(Planteles::class, 'plantel_id');
    }

    public function estadoRelation()
    {
        return $this->belongsTo(Estados::class, 'estado', 'idestado');
    }

    public function evaluadores()
    {
        return $this->belongsToMany(User::class, 'concurso_evaluador', 'concurso_id', 'evaluador_id')
                    ->where('rol', 'evaluador');
    }

    public function criterios()
    {
        return $this->hasMany(CriteriosEvaluacion::class, 'concurso_id');
    }

    public function getEstadoNombreAttribute()
    {
        if ($this->fase === 'estatal') {
            return $this->estadoRelation->nombre ?? 'No especificado';
        }
        return null;
    }

    public function getPlantelNombreAttribute()
    {
        return $this->plantel->nombre_corto ?? null;
    }
}