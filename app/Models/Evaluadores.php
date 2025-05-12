<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluadores extends Model
{
    protected $table = 'evaluadores';

    protected $fillable = [
        'userID',
        'perfil',
    ];

    protected $casts = [
        'perfil' => 'array', // Automatically cast JSON to array
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}
