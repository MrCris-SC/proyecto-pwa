<?php
// En App\Models\Criterio.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criterio extends Model
{
    protected $table = 'criterios_evaluacion';
    
    protected $fillable = [
        'nombre',
        'puntaje_maximo',
        'concurso_id',
        'modalidad_id',
        'tipo_criterio'
    ];

    public function concurso(): BelongsTo
    {
        return $this->belongsTo(Concursos::class, 'concurso_id');
    }

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidades::class, 'modalidad_id');
    }

    public function puntajes(): HasMany
    {
        return $this->hasMany(PuntajesEvaluacion::class, 'criterio_id');
    }
}