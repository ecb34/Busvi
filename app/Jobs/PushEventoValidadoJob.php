<?php

namespace App\Jobs;

use App\Mail\EventoSinCompanyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Services\FirebaseService;

use App\PushToken;
use App\User;
use Illuminate\Support\Facades\Mail;

class PushEventoValidadoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $evento;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($evento)
    {
        $this->evento = $evento;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FirebaseService $firebaseService)
    {

        if($this->evento->organizador && $this->evento->organizador->api_token != ''){

            $title = 'Evento validado';
            $body = 'El evento '.$this->evento->nombre.' ha sido validado';
            $data = [
                'tipo' => 'evento_validado',
                'evento_id' => $this->evento->id,
                'token' => $this->evento->organizador->api_token,
            ];

            foreach ($this->evento->organizador->getTokens as $token){
                $firebaseService->sendNotification($token->push_token, $title, $body, $data);
            }

        }    

    }
}
