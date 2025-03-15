<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Services\FirebaseService;

use App\PushToken;

class PushUpdateReservaJob implements ShouldQueue
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
    public function handle()
    {

        if($this->reserva->user->api_token != ''){

            if($this->reserva->anulado){
                $title = 'Se ha anulado una reserva';    
            } elseif($this->reserva->confirmado) {
                $title = 'Se ha confirmado una reserva';    
            } else {
                $title = 'Se ha actualizado el estado de una reserva a pendiente';    
            }

            $body = date('d/m/Y', strtotime($this->reserva->fecha)).' - '.$this->reserva->turno->nombre;
            $data = [
                'tipo' => 'reserva_actualizada',
                'reserva_id' => $this->reserva->id,
                'token' => $this->reserva->user->api_token,
            ];

            foreach ($this->reserva->user->getTokens as $token){
                $firebaseService->sendNotification($token->push_token, $title, $body, $data);
            }

        }
    }
}
