<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollectorAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $collector;

    /**
     * Crear una nueva instancia de mensaje.
     *
     * @return void
     */
    public function __construct($collector)
    {
        $this->collector = $collector;
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('¡Tu cuenta ha sido aprobada!')->view('emails.collector_accepted');
    }
}
