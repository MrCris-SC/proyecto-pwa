<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReporteConcursoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreConcurso;
    public $pdfContent;
    public $concurso;
    public $equipo;
    public $evaluaciones;

    public function __construct($pdfContent, $nombreConcurso, $concurso = null, $equipo = null, $evaluaciones = null)
    {
        $this->pdfContent = $pdfContent;
        $this->nombreConcurso = $nombreConcurso;
        $this->concurso = $concurso;
        $this->equipo = $equipo;
        $this->evaluaciones = $evaluaciones;
    }

    public function build()
    {
        return $this->subject("Resultados del concurso: {$this->nombreConcurso}")
            ->view('pdf.resultados')
            ->with([
                'concurso' => $this->concurso,
                'equipo' => $this->equipo,
                'evaluaciones' => $this->evaluaciones,
            ])
            ->attachData($this->pdfContent, 'resultados.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
