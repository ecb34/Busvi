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

use App\ChequeRegalo;

class ChequeRegaloAPIController extends Controller
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
  public function misChequesDisponibles(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user){
        return response()->json(['cheques_regalo' => $user->chequesRegaloRecibidos()->with('company', 'emisor')->where('status', '1')->get()], 200);
    }
    return response()->json(['msg' => trans('api.login_incorrecto')], 500); 

  }

  public function comprobar(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user && ($user->role == 'admin' || $user->role == 'crew') ){
        $chequeRegalo = ChequeRegalo::where('uuid',$this->params['uuid'])->with('company', 'emisor')->first();
        if(!$chequeRegalo){
            return response()->json(['msg' => trans('api.error_cheque_regalo_no_encontrado')], 500);  
        }
        if($chequeRegalo->company_id != null && $chequeRegalo->company_id != $user->company_id){
            return response()->json(['msg' => trans('api.error_cheque_regalo_distinto_negocio')], 500);  
        }
        if($chequeRegalo->status == 0){
            return response()->json(['msg' => trans('api.error_cheque_regalo_pendiente_de_pago')], 500);  
        }
        if($chequeRegalo->status == 3){
            return response()->json(['msg' => trans('api.error_cheque_regalo_usado')], 500);  
        }
        return response()->json(['cheque_regalo' => $chequeRegalo], 200);

    }
     return response()->json(['msg' => trans('api.error_permisos')], 500);  

  }

  public function consumir(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user && ($user->role == 'admin' || $user->role == 'crew') ){
           $chequeRegalo = ChequeRegalo::where('uuid',$this->params['uuid'])->with('company', 'emisor')->first();
        if(!$chequeRegalo){
            return response()->json(['msg' => trans('api.error_cheque_regalo_no_encontrado')], 500);  
        }
        if($chequeRegalo->company_id != null && $chequeRegalo->company_id != $user->company_id){
            return response()->json(['msg' => trans('api.error_cheque_regalo_distinto_negocio')], 500);  
        }
        if($chequeRegalo->status == 0){
            return response()->json(['msg' => trans('api.error_cheque_regalo_pendiente_de_pago')], 500);  
        }
        if($chequeRegalo->status == 3){
            return response()->json(['msg' => trans('api.error_cheque_regalo_usado')], 500);  
        }
        $chequeRegalo->status = 3;
        $chequeRegalo->used_at = Carbon::now();
        $chequeRegalo->company_id = $user->company_id;
        $chequeRegalo->save();
        return response()->json(['cheque_regalo' => $chequeRegalo], 200);

    }
     return response()->json(['msg' => trans('api.error_permisos')], 500);  
  }



   public function historicoConsumidos(){
    $user = \App\User::where('api_token', $this->params['token'])->first();
    if($user && ($user->role == 'admin' || $user->role == 'crew') ){
        $chequesRegalo = ChequeRegalo::with(['destinatario'])->where('status',3)->where('company_id', $user->company_id)->orderBy('used_at', 'DESC')->take(500)->get();
        if(!$chequesRegalo){
            return response()->json(['msg' => trans('api.error_cheque_regalo_no_encontrado')], 500);  
        }
        return response()->json(['cheques_regalo' => $chequesRegalo], 200);

    }
     return response()->json(['msg' => trans('api.error_permisos')], 500);  

  }
}
