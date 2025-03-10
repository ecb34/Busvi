<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;
use Response;
use Auth;
use Mail;
use App\Mail\UserMail;

use App\Evento;
use App\Jobs\PushEventoValidadoJob;

class EventosAPIController extends Controller
{
    private $params;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->params = json_decode($request->getContent(), true);
        
        if (! $this->params){
            return response()->json(['msg' => trans('api.login_incorrecto')], 500); 
        }

    }
  public function misEventosDisponibles(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user){
        return response()->json(['eventos_disponibles' => $user->eventos()
                            ->where('desde', '>=', Carbon::now()->addDays(-1))
                            ->wherePivot('pagado', 1)->wherePivot('confirmacion_asistencia', 0)
                            ->with('company',  'organizador', 'categoria')->orderBy('desde', 'desc')->get()], 200);
    }
    return response()->json(['msg' => trans('api.login_incorrecto')], 500); 

  }

  public function EventosOrganizadosPorMi(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user){
        return response()->json(['mis_eventos' => Evento::where('organizador_id',$user->id)
                                ->with('company', 'organizador', 'asistentes')->orderBy('desde', 'desc')->get()], 200);
    }
    return response()->json(['msg' => trans('api.login_incorrecto')], 500); 

  }

  public function comprobar(){
    $user = \App\User::where('api_token', $this->params['token'])->first();

    if($user){
        $evento = Evento::where('id', $this->params['evento_id'])
                    ->whereHas('asistentes', function ($q){
                        $q->where('uuid',$this->params['uuid']);
                    })->with('company', 'organizador', 'categoria', 'asistentes')->first();
        if(!$evento){
            return response()->json(['msg' => trans('api.error_evento_no_encontrado')], 500);  
        }
        if($evento->organizador_id != $user->id){
            return response()->json(['msg' => trans('api.error_evento_permisos')], 500);  
        }        
        if($evento->validado == 0){
            return response()->json(['msg' => trans('api.error_evento_pendiente_validar')], 500);  
        }
        $asistencia = $evento->asistentes()->wherePivot('uuid', $this->params['uuid'])->first();
        return response()->json(['asistencia' => $asistencia], 200);

    }
     return response()->json(['msg' => trans('api.error_permisos')], 500);  

  }

  public function consumir(){
     $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user){
        $evento = Evento::where('id', $this->params['evento_id'])
                    ->whereHas('asistentes', function ($q){
                        $q->where('uuid',$this->params['uuid']);
                    })->with('company', 'organizador', 'categoria', 'asistentes')->first();
        if(!$evento){
            return response()->json(['msg' => trans('api.error_evento_no_encontrado')], 500);  
        }
        if($evento->organizador_id != $user->id){
            return response()->json(['msg' => trans('api.error_evento_permisos')], 500);  
        }        
        if($evento->validado == 0){
            return response()->json(['msg' => trans('api.error_evento_pendiente_validar')], 500);  
        }
        $asistencia = $evento->asistentes()
                        ->wherePivot('uuid', $this->params['uuid'])
                        ->first();

        $sql = 'update cliente_evento set confirmacion_asistencia=?, ultimo_uso_entrada=? where evento_id=? and uuid=?';
        \DB::update($sql, [1, date('Y-m-d H:i:s'), $evento->id, $this->params['uuid']]);

        // si lo hacemos asi se validan a la vez todas las entradas que tenga el usuario
        // $asistencia = $evento->asistentes()->updateExistingPivot($asistencia->id, ['confirmacion_asistencia' => 1, 'ultimo_uso_entrada' => Carbon::now()]);

        return response()->json(['asistencia' => $asistencia], 200);

    }
     return response()->json(['msg' => trans('api.error_permisos')], 500);  

  }

   public function historicoConsumidos(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user){
        $eventos = $user->eventos()
                    ->where('desde', '<=', Carbon::now()->addDays(-1))
                    ->orWherePivot('confirmacion_asistencia',1)
                    ->orderBy('eventos.desde', 'DESC')->take(500)->get();
        if(!$eventos){
            return response()->json(['msg' => trans('api.error_evento_no_encontrado')], 500);  
        }
        return response()->json(['eventos' => $eventos], 200);

    }
     return response()->json(['msg' => trans('api.error_permisos')], 500);  
  }

  public function negocioMisEventos(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user && ($user->role == 'admin' || $user->role == 'crew') ){
        return response()->json(['eventos_disponibles' => Evento::where('company_id', $user->company_id)
                            ->with('organizador', 'categoria')
                            ->orderBy('desde', 'DESC')->get()], 200);
    }
    return response()->json(['msg' => trans('api.login_incorrecto')], 500); 

  }

  public function validar(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user && ($user->role == 'admin' || $user->role == 'crew') ){
        $evento = Evento::where('id', $this->params['evento_id'])->first();
        if(!$evento){
            return json_encode(['evento' => null, 'status' => '404']);
        }
        if($evento->company_id == $user->company_id || \Auth::user()->role == 'admin'){
            
            $evento->validado =  $this->params['validado'];
            $evento->save();

            if($evento->validado){
                dispatch(new PushEventoValidadoJob($evento));
            }

            $message = 'Evento disponible para su uso.';
            $m_status = 'success';
        }else{
            $message = 'No tiene permisos para hacer esta acción.';
            $m_status = 'error';
        }
        
        return response()->json(['msg' => 'ok'], 200); 
    }
    return response()->json(['msg' => trans('api.error_permisos')], 500);  
  }

}
