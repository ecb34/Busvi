<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Services\FirebaseService;


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
    public function handle(FirebaseService $firebaseService)
    {
        $firebaseService->sendNotification($this->token, $this->data['title'], $this->data['body'], $this->data);
    }
}
