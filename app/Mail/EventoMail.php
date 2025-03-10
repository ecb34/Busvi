<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventoMail extends Mailable
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
        $this->data = $evento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Te has inscrito al evento Busvi: ' . $this->data->nombre)
                    ->view('emails.eventos.inscrito')
                    ->with('data', $this->data);
    }
}