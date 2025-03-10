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

class PushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($token, $data)
    {
        $this->token = $token;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($this->data['title']);
        $notificationBuilder->setBody($this->data['body'])
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($this->data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = $this->token;

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        foreach($downstreamResponse->tokensToDelete() as $token){
            $token = \App\PushToken::where('push_token', $token)->delete();
        }

        // $aux = $downstreamResponse->numberSuccess();
        // $aux2 = $downstreamResponse->numberFailure();
        // $aux3 = $downstreamResponse->numberModification();
    }
}
