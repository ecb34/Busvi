<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Jobs\PushRememberJob;
use App\Event;
use App\Reserva;

class SendRemembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_remembers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar recordatorios';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$date1 = Carbon::tomorrow();
    	$date2 = Carbon::tomorrow()->addDay()->subSecond();

    	$events = Event::whereBetween('start_date', [$date1, $date2])
				       ->get();

		foreach ($events as $event){
            if($event->user->api_token != ''){
            
                $tokens = $event->user->getTokens()->orderBy('date', 'DESC')->get();
                $title = Carbon::parse($event->start)->format('d/m H:i') . ' - ' . $event->service->name;

                foreach($tokens as $token){
                    $data = [
                        'tipo' => 'recordatorio_cita',
                        'event_id' => $event->id,
                        'token' => $event->user->api_token,
                        'title' => 'Busvi',
                        'body' => 'Le recordamos que mañana tiene una cita para ' . $event->service->name
                    ];
                    dispatch(new PushRememberJob($token->push_token, $data));
                }

            }
        }

        $reservas = Reserva::where('confirmado', 1)->where('fecha', $date1->toDateString())->get();
        foreach ($reservas as $reserva){
            if($reserva->user->api_token != ''){

                $tokens = $reserva->user->getTokens()->orderBy('date', 'DESC')->get();
                $title = Carbon::parse($reserva->fecha)->format('d/m') . ' - Reserva ' . $reserva->turno->company->name_comercial;

                foreach($tokens as $token){
                    $data = [
                        'tipo' => 'recordatorio_reserva',
                        'reserva_id' => $reserva->id,
                        'token' => $reserva->user->api_token,
                        'title' => 'Busvi',
                        'body' => 'Le recordamos que mañana tiene una reserva en ' . $reserva->turno->company->name_comercial
                    ];
                    dispatch(new PushRememberJob($token->push_token, $data));
                }

            }
		}

        $this->comment('enviar recordatorios');
    }
}
