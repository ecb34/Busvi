<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Services\FirebaseService;

use App\PushToken;

class PushCreateReservaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reserva;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FirebaseService $firebaseService)
    {

        if($this->reserva->turno->company->admin->api_token != ''){

            $title = 'Se ha creado una nueva reserva';
            $body = date('d/m/Y', strtotime($this->reserva->fecha)).' - '.$this->reserva->turno->nombre;
            $data = [
                'tipo' => 'nueva_reserva',
                'reserva_id' => $this->reserva->id,
                'token' => $this->reserva->turno->company->admin->api_token,
            ];

            foreach ($this->reserva->turno->company->admin->getTokens as $token){
                $firebaseService->sendNotification($token->push_token, $title, $body, $data);
            }

        }
    }
}
