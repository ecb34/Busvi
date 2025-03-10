<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class FichajeController extends Controller
{
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
        
        if (! $this->params){
            return response()->json(['msg' => trans('api.login_incorrecto')], 500); 
        }

    }

    public function postListado(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        if($user){
            $fichajes = \App\Fichaje::where('user_id', $user->id)->orderBy('id', 'desc')->take(100)->get();
            return response()->json(['fichajes' => $fichajes], 200);
        }

        return response()->json(['msg' => trans('api.error_get_fichajes')], 500);

    }

    public function postInicio(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        if($user){

            // hay algun fichaje abierto?

            $fichaje = \App\Fichaje::where('user_id', $user->id)->whereNull('fin')->first();
            if(is_null($fichaje)){

                $inicio = date('Y-m-d H:i:s');

                // el fichaje ya existe?

                $fichaje = \App\Fichaje::where('user_id', $user->id)->where('inicio', $inicio)->first();
                if(is_null($fichaje)){

                    \App\Fichaje::create([
                        'user_id' => $user->id,
                        'inicio' => $inicio,
                        'fin' => null,
                        'posicion_inicio' => isset($this->params['posicion']) ? $this->params['posicion'] : '',
                        'posicion_fin' => '',
                    ]);

                    return $this->postListado();

                } else {
                    return response()->json(['msg' => trans('api.error_abriendo_fichaje')], 500);            
                }

            } else {
                return response()->json(['msg' => trans('api.error_abriendo_fichaje')], 500);        
            }

            
        }

        return response()->json(['msg' => trans('api.error_get_fichajes')], 500);

    }

    public function postFin(){

        $user = \App\User::where('api_token', $this->params['token'])->first();
        if($user){

            // hay algun fichaje abierto? los cerramos todos

            $fichajes = \App\Fichaje::where('user_id', $user->id)->whereNull('fin')->get();
            if(count($fichajes) > 0){
                foreach($fichajes as $fichaje){

                    $fichaje->fin = date('Y-m-d H:i:s');
                    $fichaje->posicion_fin = isset($this->params['posicion']) ? $this->params['posicion'] : '';
                    $fichaje->save();

                }
                
                return $this->postListado();

            } else {
                return response()->json(['msg' => trans('api.error_no_fichaje_abierto')], 500);        
            }
   
        }

        return response()->json(['msg' => trans('api.error_get_fichajes')], 500);

    }
    
}
