<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

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

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'tipo' => 'reserva_actualizada',
                'reserva_id' => $this->reserva->id,
                'token' => $this->reserva->user->api_token,
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            foreach ($this->reserva->user->getTokens as $token){
                $downstreamResponse = FCM::sendTo($token->push_token, $option, $notification, $data);
                foreach($downstreamResponse->tokensToDelete() as $token_delete){
                    \App\PushToken::where('push_token', $token_delete)->delete();
                }
            }

        }
    }
}
