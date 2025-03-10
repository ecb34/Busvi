<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Chat;
use App\User;
use App\Event;
use App\Reserva;
use App\PushToken;

use App\Jobs\PushJob;

class ChatController extends Controller
{
    private $params;

    private $LIMIT = 500;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->params = json_decode($request->getContent(), true);
        
        if (! $this->params)
        {
            return response()->json(['msg' => trans('api.login_incorrecto')], 500); 
        }

        if(!isset($this->params['tipo'])){
            $this->params['tipo'] = 'cita';
        }

        $this->setLang($this->params['lang']);
    }

    public function history()
    {
        if ($this->validToken($this->params['token']))
        {
            $messages = new Chat;

            switch($this->params['tipo']){
                case 'cita':
                    $messages = $messages->where('event_id', $this->params['event_id']);
                break;
                case 'reserva':
                    $messages = $messages->where('reserva_id', $this->params['reserva_id']);
                break;
            }

	    	if ($this->params['id_message'] > 0)
	    	{
	    		$messages = $messages->where('id', '<=', $this->params['id_message'] + 5);
	    	}
	    	
            $messages = $messages->orderBy('id', 'DESC')
					    		 ->limit($this->LIMIT)
					    		 ->get();

			return response()->json(['messages' => $messages], 200);
        }

        return response()->json(['msg' => trans('api.error_history')], 500);
    }

    public function message()
    {
        $user = $this->validToken($this->params['token']);
        
        if ($user && ($this->params['text'] != '' && $this->params['text'] != NULL))
        {
	    	$message = new Chat;

            switch($this->params['tipo']){
                case 'cita':
                    $message->event_id = $this->params['event_id'];
                break;
                case 'reserva':
                    $message->reserva_id = $this->params['reserva_id'];
                break;
            }
	    	
	    	$message->user_id = $user->id;
	    	$message->role = $user->role;
	    	$message->text = $this->params['text'];

	    	if ($message->save())
	    	{
                $qty_push = $this->sendPushJob($message, $user);

				return response()->json(['message' => $message, 'qty_push' => $qty_push], 200);
	    	}
        }

        return response()->json(['msg' => trans('api.error_message')], 500);
    }

    /**
     * Obtenemos la lista de chats para el admin o el crew.
     * Eso es que obtenemos todos los eventos que tengan chat y pertenezcan
     * al user id correspondiente.
     *
     * @return Array que contiene el evento con el servicio, el nombre del cliente
     *         y el texto del último mensaje.
     */
    public function crewList()
    {
        $user = $this->validToken($this->params['token']);
        
        if ($user)
        {
            $chats = [];

            if ($user->role == 'crew' || $user->role == 'admin')
            {

                // citas

                $events = Event::has('chat')
                               ->where('user_id', $user->id)
                               ->with('service', 'customer')
                               ->get();

                foreach ($events as $event)
                {
                    $event_temp = Event::find($event->id);
                    $message = $event_temp->chat()
                                          ->orderBy('created_at', 'DESC')
                                          ->first();
                                          
                    $chats[] = [
                                'tipo' => 'cita',
                                'event' => $event,
                                'text'  => $message->text,
                                'date'  => $message->created_at
                                ];
                }

            }

            if ($user->role == 'admin')
            {

                // reservas

                $reservas = Reserva::has('chat')
                                ->with('turno.company.admin', 'user')
                                ->whereNotNull('user_id')
                                ->whereHas('turno.company.admin', function($q) use ($user) {
                                    $q->where('id', $user->id);
                                })
                               ->get();

                foreach ($reservas as $reserva)
                {
                    $reserva_temp = Reserva::find($reserva->id);
                    $message = $reserva_temp->chat()
                                          ->orderBy('created_at', 'DESC')
                                          ->first();
                                          
                    $chats[] = [
                                'tipo' => 'reserva',
                                'reserva' => $reserva,
                                'text'  => $message->text,
                                'date'  => $message->created_at
                                ];
                }

            }
            
            return response()->json(['chats' => $chats], 200);
        }

        return response()->json(['msg' => trans('api.crew_list')], 500);
    }

    private function validToken($token)
    {
        $user = User::where('api_token', $token)->first();

        if ($user)
        {
            return $user;
        }

        return NULL;
    }

    private function setLang($lang)
    {
        if (isset($lang))
        {
            \App::setLocale($lang);
        }
    }

    private function sendPushJob($message, $user)
    {

        $event = $message->event;
        $reserva = $message->reserva;

        if(!is_null($event)){
            $time = $user->name . ' - ' . $event->service->name . ' - ' . Carbon::parse($event->start)->format('d/m H:i');
            if ($user->role == 'user'){
                $receiver = $event->user;
            } else {
                $receiver = $event->customer;
            }
        } 

        if(!is_null($reserva)){
            $time = $user->name . ' - Reserva ' . $reserva->turno->company->name_comercial . ' - ' . Carbon::parse($reserva->fecha)->format('d/m');
            if ($user->role == 'user'){
                $receiver = $reserva->turno->company->admin;
            } else {
                $receiver = $reserva->user;
            }
        }

        $data = [
                    'tipo' => 'chat',
                    'event_id' => $message->event_id,
                    'reserva_id' => $message->reserva_id,
                    'token' => $receiver->api_token,
                    'title' => $time,
                    'body' => $message->text
                ];

        $qty_push = 0;

        foreach ($receiver->getTokens as $token)
        {
            dispatch(new PushJob($token->push_token, $data));

            $qty_push++;
        }

        return $qty_push;
    }
}
