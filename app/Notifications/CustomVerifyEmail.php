<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
class CustomVerifyEmail extends BaseVerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    #public function via(object $notifiable): array
    #{
    #    return ['mail'];
    #}

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirma tu correo electrónico')
            ->greeting('Hola ' . $notifiable->name . '!')
            ->line('Gracias por registrarte en nuestra plataforma. Por favor, confirma tu correo electrónico haciendo clic en el botón de abajo.')
            ->action('Verificar Correo', $verificationUrl)
            ->line('Si no creaste esta cuenta, ignora este mensaje.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
