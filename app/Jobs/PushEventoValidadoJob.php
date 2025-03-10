<?php

namespace App\Jobs;

use App\Mail\EventoSinCompanyMail;
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
    public function handle()
    {

        if($this->evento->organizador && $this->evento->organizador->api_token != ''){

            $title = 'Evento validado';
            $body = 'El evento '.$this->evento->nombre.' ha sido validado';
            
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'tipo' => 'evento_validado',
                'evento_id' => $this->evento->id,
                'token' => $this->evento->organizador->api_token,
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            foreach ($this->evento->organizador->getTokens as $token){
                $downstreamResponse = FCM::sendTo($token->push_token, $option, $notification, $data);
                foreach($downstreamResponse->tokensToDelete() as $token_delete){
                    \App\PushToken::where('push_token', $token_delete)->delete();
                }
            }

        }    

    }
}
