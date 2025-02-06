<?php

namespace App\Listeners;

use App\Events\UsuarioRegistrado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RedirigirUsuarioParaCompletarPerfil
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    
    public function handle(UsuarioRegistrado $event)
    {
        $user = $event->user;

        // Si el usuario NO es administrador, redirigir a la página de completar perfil
        if ($user->rol !== 'admin') {
            session(['completar_perfil' => true]);
        }
    }
}
