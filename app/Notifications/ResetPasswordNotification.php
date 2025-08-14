<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPasswordBase
{
    use Queueable;

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restablecer contraseña')
            ->line('Has solicitado restablecer la contraseña de tu cuenta.')
            ->line('Haz clic en el siguiente botón para continuar con el proceso:')
            ->action('Restablecer Contraseña', url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)))
            ->line('Si no solicitaste este cambio, no es necesario realizar ninguna acción.');
    }
}
