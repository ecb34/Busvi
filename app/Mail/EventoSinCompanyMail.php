<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventoSinCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $evento;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($evento)
    {
        $this->evento = $evento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nuevo evento sin negocio: ' . $this->evento->nombre)
                    ->view('emails.eventos.nuevo_sin_company')
                    ->with('evento', $this->evento);
    }
}