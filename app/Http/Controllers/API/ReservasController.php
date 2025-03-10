<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\PushCreateReservaJob;
use App\Jobs\PushAnularReservaJob;
use App\Jobs\PushUpdateReservaJob;
use Carbon\Carbon;

class ReservasController extends Controller {

    private $params;
    private $LIMIT = 100;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->params = json_decode($request->getContent(), true);
        
        if (!$this->params){
            return response()->json(['msg' => trans('api.login_incorrecto')], 500); 
        }

    }

    public function postTurnosDisponiblesAdmin(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        if($user->role != 'admin'){
            abort(500);
        }

        $this->params['company_id'] = $user->company_id;
        return $this->postTurnosDisponibles();

    }

    public function postTurnosDisponibles(){

        $start = mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']);
        $end = strtotime('+1 month', $start);

        // $manana = strtotime('+1 day', strtotime(date('Y-m-d')));
        // $start = max($start, $manana);
        $hoy = strtotime(date('Y-m-d'));
        $start = max($start, $hoy);

        if($end <= $start){
            return response()->json(['events' => []]);
        }

        if($end - $start > 50 * 24 * 60 * 60){ // más de 50 dias
            $end = strtotime('+50 day', $start);
        }

        $company = \App\Company::find($this->params['company_id']);
        if(is_null($company) || $company->type != 1 || $company->payed == 0 || !$company->enable_reservas){
            abort(404);
        }

        $eventos = [];
        $turnos = $company->turnos;

        // limpiamos posibles caches
        $dia = $start;
        do {
            foreach($turnos as $turno){
                $turno->reset_cache($dia);
            }
            $dia = strtotime('+1 day', $dia);
        } while($dia <= $end);
        
        $dia = $start;
        do  {

            foreach($turnos as $turno){
                
                $plazas_disponibles = $turno->fecha_disponible($dia);
                if($plazas_disponibles !== false && $plazas_disponibles > 0){

                    $eventos[] = [
                        'turno' => $turno,
                        'plazas' => $turno->plazas,
                        'plazas_disponibles' => $plazas_disponibles,
                        'date' => date('Y-m-d', $dia),
                    ];

                }

            }

            $dia = strtotime('+1 day', $dia);
        } while($dia <= $end);

        return response()->json(['events' => $eventos]);

    }

    public function postSolicitarReserva(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        $turno = \App\Turno::find($this->params['turno_id']);
        $company = $turno->company;
        
        if(is_null($user) || $user->role != 'user' || is_null($turno) || is_null($company) || $company->type != 1 || $company->enable_reservas != 1){
            return response()->json(['msg' => trans('api.error_crear_reserva')], 500); 
        }

        $fecha = strtotime(str_replace('/', '-', $this->params['fecha']));
        $turno->reset_cache($fecha);
        if(!$turno->fecha_disponible($fecha)){
            return response()->json(['msg' => trans('api.error_crear_reserva')], 500); 
        }

        $plazas = intval($this->params['plazas']);
        if(!$turno->fecha_disponible($fecha, $plazas)){
            return response()->json(['msg' => trans('api.error_crear_reserva_faltan_plazas')], 500); 
        }

        $reserva = \App\Reserva::create([
            'user_id' => $user->id,
            'turno_id' => $turno->id,
            'fecha' => date('Y-m-d', $fecha),
            'plazas' => $plazas
        ]);

        if($reserva){
            dispatch(new PushCreateReservaJob($reserva));
            return response()->json('ok', 200); 
        } else {
            return response()->json(['msg' => trans('api.error_crear_reserva')], 500); 
        }

    }

    public function postSolicitarReservaAdmin(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        $turno = \App\Turno::find($this->params['turno_id']);
        $company = $user->company;
        
        if(is_null($user) || $user->role != 'admin' || is_null($turno) || is_null($company) || $company->type != 1 || $company->enable_reservas != 1){
            return response()->json(['msg' => trans('api.error_crear_reserva')], 500); 
        }

        $fecha = strtotime(str_replace('/', '-', $this->params['fecha']));
        $turno->reset_cache($fecha);
        if(!$turno->fecha_disponible($fecha)){
            return response()->json(['msg' => trans('api.error_crear_reserva')], 500); 
        }

        $plazas = intval($this->params['plazas']);
        if(!$turno->fecha_disponible($fecha, $plazas)){
            return response()->json(['msg' => trans('api.error_crear_reserva_faltan_plazas')], 500); 
        }

        $nombre = isset($this->params['nombre']) && !is_null($this->params['nombre']) ? trim($this->params['nombre']) : '';
        if($nombre == ''){
            return response()->json(['msg' => trans('api.error_crear_reserva_falta_nombre')], 500); 
        }

        $telefono = isset($this->params['telefono']) && !is_null($this->params['telefono']) ? trim($this->params['telefono']) : '';
        if($telefono == ''){
            return response()->json(['msg' => trans('api.error_crear_reserva_falta_telefono')], 500); 
        }

        $reserva = \App\Reserva::create([
            'user_id' => null,
            'turno_id' => $turno->id,
            'fecha' => date('Y-m-d', $fecha),
            'plazas' => $plazas,
            'nombre' => $nombre,
            'telefono' => $telefono,
            'confirmado' => true,
        ]);

        if($reserva){
            return response()->json('ok', 200); 
        } else {
            return response()->json(['msg' => trans('api.error_crear_reserva')], 500); 
        }

    }

    public function postReservasUsuario(){
        
        $user = \App\User::where('api_token', $this->params['token'])->first();
        
        if(is_null($user) || $user->role != 'user'){
            return response()->json(['msg' => trans('api.error_recuperando_reservas')], 500); 
        }

        $reservas = \App\Reserva::with('turno.company')->where('user_id', $user->id)->orderBy('fecha', 'desc')->get();
        return response()->json(['reservas' => $reservas]);

    }

    public function postReservasAdmin(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        
        if(is_null($user) || $user->role != 'admin'){
            return response()->json(['msg' => trans('api.error_recuperando_reservas')], 500); 
        }

        $inicio = mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']);
        $fin = strtotime('+1 month', $inicio);

        $reservas = \App\Reserva::with(['turno.company', 'user'])->where('fecha', '>=', date('Y-m-d', $inicio))->where('fecha', '<=', date('Y-m-d', $fin))->whereHas('turno', function($q) use($user) {
            $q->where('company_id', $user->company_id);
        })->orderBy('fecha', 'desc')->get();

        return response()->json(['reservas' => $reservas]);

    }

    public function postAnularReservaUsuario(){
        
        $user = \App\User::where('api_token', $this->params['token'])->first();
        $reserva = \App\Reserva::find($this->params['reserva_id']);

        if(is_null($user) || is_null($reserva) || $reserva->user->id != $user->id){
            return response()->json(['msg' => trans('app.reservas.error_anulando_reserva')], 500); 
        }

        $reserva->confirmado = false;
        $reserva->anulado = true;

        if($reserva->save()){
            dispatch(new PushAnularReservaJob($reserva));
            return response()->json('ok', 200);
        } else {
            return response()->json(['msg' => trans('app.reservas.error_anulando_reserva')], 500); 
        }

    }

    public function postEstadoReservaAdmin(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        $reserva = \App\Reserva::find($this->params['reserva_id']);

        if(is_null($user) || is_null($reserva) || $reserva->turno->company->id != $user->company_id){
            return response()->json(['msg' => trans('app.reservas.error_cambiando_estado_reserva')], 500); 
        }

        switch($this->params['estado']){
            case 'pendiente':
                $reserva->confirmado = false;
                $reserva->anulado = false;
            break;
            case 'anulada':
                $reserva->confirmado = false;
                $reserva->anulado = true;
            break;
            case 'confirmada':
                $reserva->confirmado = true;
                $reserva->anulado = false;
            break;
        }
        
        if($reserva->save()){
            dispatch(new PushUpdateReservaJob($reserva));
            return response()->json($reserva, 200);
        } else {
            return response()->json(['msg' => trans('api.error_cambiando_estado_reserva')], 500); 
        }
        
    }

}