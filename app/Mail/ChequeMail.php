<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChequeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cheque;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cheque)
    {
        $this->data = $cheque;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Has recibido un Cheque Regalo Busvi: ' . $this->data->importe)
                    ->view('emails.cheque_regalo.recibido')
                    ->with('data', $this->data);
    }
}