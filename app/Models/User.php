<?php

namespace App\Models;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'genero',
        'telefono',
        'direccion',
        'estado_id',
        'municipio_id',
        'grado_estudio',
        'perfil_completo',
        'plantel_id',
        'concurso_registrado_id',
        'equipo_id',
        'has_completed_profiles',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }
    public function evaluaciones()
    {
        return $this->hasMany(Evaluaciones::class, 'evaluador_id');
    }
    public function concursosAsignados()
    {
        return $this->belongsToMany(Concursos::class, 'concurso_evaluador', 'evaluador_id', 'concurso_id');
    }
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->rol === $role;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'concurso_registrado_id' => 'integer',
            'has_completed_profiles' => 'boolean',
        ];
    }
}
