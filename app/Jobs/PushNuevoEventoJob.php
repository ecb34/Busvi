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

class PushNuevoEventoJob implements ShouldQueue
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
        if($this->evento->company){

            if($this->evento->company->admin->api_token != ''){

                $title = 'Se ha creado un evento en tu negocio';
                
                $body = $this->evento->nombre.PHP_EOL.date('d/m/Y', strtotime($this->evento->desde->format('d-m-Y H:i')));
                if($this->evento->hasta){
                    $body .= ' - '.$this->evento->hasta->format('d-m-Y H:i'); 
                }
                if($this->evento->aforo_maximo){
                    $body .= PHP_EOL.'Aforo Máximo: '.$this->evento->aforo_maximo; 
                }
                else{
                    $body .= PHP_EOL.'Aforo Máximo: Ilimitado'; 
                }
                

                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60*20);

                $notificationBuilder = new PayloadNotificationBuilder($title);
                $notificationBuilder->setBody($body)
                                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'tipo' => 'nuevo_evento',
                    'evento_id' => $this->evento->id,
                    'token' => $this->evento->company->admin->api_token,
                ]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                foreach ($this->evento->company->admin->getTokens as $token){
                    $downstreamResponse = FCM::sendTo($token->push_token, $option, $notification, $data);
                    foreach($downstreamResponse->tokensToDelete() as $token_delete){
                        \App\PushToken::where('push_token', $token_delete)->delete();
                    }
                }

            }    

        } else {

            // si el evento no tiene company se envia un mail a los administradores

            $admins = User::where('role', 'operator')->orWhere('role', 'superadmin')->get();
            $emails = [];
            foreach ($admins as $admin){
                Mail::to($admin->email)->send(new EventoSinCompanyMail($this->evento));
            }

        }

    }
}
