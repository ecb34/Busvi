<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GetDownCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company)
    {
        $this->data = $company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Solicitud Baja: ' . $this->data->id . ' - ' . $this->data->name)
                    ->view('emails.company.get_down')
                    ->with('data', $this->data);
    }
}