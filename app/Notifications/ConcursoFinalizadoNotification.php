<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Concursos;
use Illuminate\Support\Facades\Storage;

class ConcursoFinalizadoNotification extends Notification
{
    use Queueable;

    public $concurso;
    public $pdfPath;

    public function __construct(Concursos $concurso, $pdfPath = null)
    {
        $this->concurso = $concurso;
        $this->pdfPath = $pdfPath;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Concurso finalizado: ' . $this->concurso->nombre)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El concurso "' . $this->concurso->nombre . '" ha finalizado.')
            ->action('Ver resultados', url('/concursos/podio/' . $this->concurso->id))
            ->line('Gracias por participar.');

        if ($this->pdfPath && Storage::exists($this->pdfPath)) {
            $mail->attach(Storage::path($this->pdfPath));
        }

        return $mail;
    }
}
