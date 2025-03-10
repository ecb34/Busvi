<?php

namespace App\Http\Controllers\Admin;

use App\BloqueoTurno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\PushAnularReservaJob;
use App\Jobs\PushCreateReservaJob;
use App\Jobs\PushUpdateReservaJob;
use Carbon\Carbon;

use Validator;
use Response;

use \App\Turno;

class ReservasController extends Controller {

    public function getCalendario(){
        return view('admin/reservas/calendario_reservas');
    }

    public function getEventosCalendario(Request $request){
        
        $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',
        ]);

        if($validator->fails()){
            abort(500);
        }

        $start = strtotime($request->start);
        $end = strtotime($request->end);

        if($end <= $start){
            abort(500);
        }

        if($end - $start > 50 * 24 * 60 * 60){ // más de 50 dias
            abort(500);
        }

        $company = \App\Company::find(\Auth::user()->company->id);
        if(is_null($company)){
            abort(404);
        }

        $eventos = [];
        $turnos = $company->turnos;

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
                $plazas_ocupadas = $turno->plazas - $plazas_disponibles;

                if($plazas_disponibles !== false){

                    $eventos[] = [
                        'title' => '<strong>'.$turno->nombre.'</strong><br/><small>'.substr($turno->inicio, 0, 5).' - '.substr($turno->fin, 0, 5).'<br/>'.$plazas_ocupadas.'/'.$turno->plazas.' '.trans('app.reservas.pl_ocupadas').'</small>',
                        'start' => date('Y-m-d', $dia),
                        'end' => date('Y-m-d', $dia),
                        'vista' => 'resumen',
                        'turno_id' => $turno->id,
                        'plazas' => $turno->plazas,
                        'plazas_disponibles' => $plazas_disponibles,
                    ];

                    if($plazas_ocupadas > 0){
                        $reservas = \App\Reserva::where('turno_id', $turno->id)->where('anulado', 0)->where('fecha', date('Y-m-d', $dia))->get();
                        foreach($reservas as $reserva){
                            $estado = $reserva->confirmado ? 'Confirmada' : 'Pendiente';

                            $nombre = $reserva->nombre;
                            $telefono = $reserva->telefono;
                            $email = '';

                            if(!is_null($reserva->user)){
                                $nombre = $reserva->user->name.' '.$reserva->user->surname;
                                $telefono = $reserva->user->phone;
                                $email = $reserva->user->email;
                            }

                            $title = '<strong>'.$nombre.'</strong><br/><small>Turno: '.$turno->nombre.'<br/>Plazas: '.$reserva->plazas.'<br/>Estado: '.$estado.'<br/>Telf: '.$telefono;

                            if(!is_null($reserva->user)){
                                $title .= '<br/>Email: '.$email.'</small>';
                            } else {
                                $title .= '<br/>Reserva manual';
                            }

                            $eventos[] = [
                                'title' => $title,
                                'start' => date('Y-m-d', $dia),
                                'end' => date('Y-m-d', $dia),
                                'vista' => 'detalle',
                            ];  

                        }
                    }

                }

            }

            $dia = strtotime('+1 day', $dia);
        } while($dia <= $end);

        return response()->json($eventos);

    }

    public function postEstadoReserva(Request $request){
        
        $user = \Auth::user();
        $company = \Auth::user()->company;
        $reserva = \App\Reserva::find($request->id);

        if($reserva->turno->company_id != $company->id){
            return response()->json(['error' => trans('app.reservas.reserva_desconocida')], 500);
        }

        switch($request->accion){
            case 'anular':
                $reserva->confirmado = false;
                $reserva->anulado = true;
            break;
            case 'confirmar':
                $reserva->confirmado = true;
                $reserva->anulado = false;
            break;
            case 'pendiente':
                $reserva->confirmado = false;
                $reserva->anulado = false;
            break;
        }
        
        if($reserva->save()){
            dispatch(new PushUpdateReservaJob($reserva));
            return response()->json('ok', 200);
        } else {
            return response()->json(['error' => trans('app.reservas.error_actualizando_reserva')], 500);
        }

    }

    public function getReservas(){
        return view('admin/reservas/listado_reservas');
    }

    public function getReservasDatatables(Request $request){
        
        $company = \Auth::user()->company;

        $draw = intval($request->get('draw', ''));
		$start = intval($request->get('start', 0));
		$length = intval($request->get('length', 10));

		$output = new \StdClass;
		$output->draw = $draw;
		$output->recordsTotal = 0;
		$output->recordsFiltered = 0;
        $output->data = [];

        $query = \App\Reserva::with(['turno', 'user'])->where('fecha', '>=', date('Y-m-d'))->whereHas('turno', function($q) use ($company){
            return $q->where('company_id', $company->id);
        });

        $output->recordsTotal = $query->count();

        $estado = !is_null($request->estado) ? $request->estado : 'pendientes';
        switch($estado){
            case 'anuladas':
                $query = $query->where('anulado', 1);
            break;
            case 'pendientes':
                $query = $query->where('confirmado', 0)->where('anulado', 0);
            break;
            case 'confirmadas':
                $query = $query->where('confirmado', 1)->where('anulado', 0);
            break;
        }

        if(trim($request->search['value']) != ''){
			$search = explode(' ', trim($request->search['value']));
			foreach($search as $s){
				if($s != ''){

                    $query = $query->where(function($q) use ($s){

                        return $q
                            ->whereHas('turno', function($q2) use ($s){
                                return $q2
                                    ->where('nombre', 'like', '%'.$s.'%')
                                    ->orWhere('descripcion', 'like', '%'.$s.'%');
                            })
                            ->orWhereHas('user', function($q2) use ($s){
                                return $q2
                                    ->where('name', 'like', '%'.$s.'%')
                                    ->orWhere('surname', 'like', '%'.$s.'%')
                                    ->orWhere('email', 'like', '%'.$s.'%')
                                    ->orWhere('phone', 'like', '%'.$s.'%');
                            });

                    });

				}
			}
        }

        $output->recordsFiltered = $query->count();

		switch($request->get('order')[0]['column']){
            case '0': $query = $query->orderBy('fecha', $request->get('order')[0]['dir']); break;
        }
        
        $query = $query->skip($start)->take($length)->get();
		foreach($query as $row){

            $row->email = !is_null($row->user) ? $row->user->email : '';
            $row->estado = $row->id;
            $row->acciones = $row->id;
            $output->data[] = $row;

		}

		return Response::json($output);

    }

    public function getTurnos(){
        return view('admin/reservas/listado_turnos');
    }

    public function getTurnosDatatables(Request $request){

        $user = \Auth::user();

        $draw = intval($request->get('draw', ''));
		$start = intval($request->get('start', 0));
		$length = intval($request->get('length', 10));

		$output = new \StdClass;
		$output->draw = $draw;
		$output->recordsTotal = \App\Turno::where('company_id', $user->company->id)->count();
		$output->recordsFiltered = $output->recordsTotal;
		$output->data = [];

        $query = \App\Turno::where('company_id', $user->company->id);

        if(trim($request->search['value']) != ''){
			$search = explode(' ', trim($request->search['value']));
			foreach($search as $s){
				if($s != ''){

                    $query = $query->where(function($q) use ($s){
                        return $q->where('nombre', 'like', '%'.$s.'%');
                    });

				}
			}
        }

        $output->recordsFiltered = $query->count();

		switch($request->get('order')[0]['column']){
            case '0': $query = $query->orderBy('nombre', $request->get('order')[0]['dir']); break;
            case '4': $query = $query->orderBy('plazas', $request->get('order')[0]['dir']); break;
        }
        
        $query = $query->skip($start)->take($length)->get();
		foreach($query as $row){

            $row->dias = $row->id;
            $row->acciones = $row->id;
            $output->data[] = $row;

		}

		return Response::json($output);

    }

    public function getNuevoTurno(Request $request){
        return view('admin/reservas/nuevo_turno');
    }

    public function postNuevoTurno(Request $request){
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'plazas' => 'required|integer|min:1',
        ]);

        if($validator->fails()){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => $validator->errors()->first(), 'm_status' => 'error']);
        }

        $hora_inicio = new Carbon($request->hora_inicio);
        $hora_fin = new Carbon($request->hora_fin);

        if($hora_inicio->greaterThanOrEqualTo($hora_fin)){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => trans('app.reservas.hora_fin_anterior_inicio'), 'm_status' => 'error']);
        }

        $fecha_inicio = $request->has('fecha_inicio') && !is_null($request->fecha_inicio) ? strtotime(str_replace('/', '-', $request->fecha_inicio)) : null;
        $fecha_fin = $request->has('fecha_fin') && !is_null($request->fecha_fin) ? strtotime(str_replace('/', '-', $request->fecha_fin)) : null;

        if(!is_null($fecha_inicio) && !is_null($fecha_fin) && $fecha_fin < $fecha_inicio){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => trans('app.reservas.fecha_fin_anterior_inicio'), 'm_status' => 'error']);
        }
        
        $company = \Auth::user()->company;

        $turno = \App\Turno::where('company_id', $company->id)->where('nombre', trim($request->nombre))->first();
        if(!is_null($turno)){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => trans('app.reservas.turno_repetido'), 'm_status' => 'error']);
        }

        $turno = Turno::create([
            'company_id' => $company->id,
            'nombre' => trim($request->nombre),
            'inicio' => $hora_inicio->toTimeString(),
            'fin' => $hora_fin->toTimeString(),
            'plazas' => $request->plazas,
            'descripcion' => $request->descripcion,
            'lunes' => $request->has('lunes'),
            'martes' => $request->has('martes'),
            'miercoles' => $request->has('miercoles'),
            'jueves' => $request->has('jueves'),
            'viernes' => $request->has('viernes'),
            'sabado' => $request->has('sabado'),
            'domingo' => $request->has('domingo'),
            'fecha_inicio' => !is_null($fecha_inicio) ? date('Y-m-d', $fecha_inicio) : null,
            'fecha_fin' => !is_null($fecha_fin) ? date('Y-m-d', $fecha_fin) : null,
        ]);

        if(!is_null($turno)){
            return redirect()->action('Admin\ReservasController@getTurnos')->with(['message' => trans('app.reservas.turno_creado'), 'm_status' => 'success']);
        } else {
            return redirect()->action('Admin\ReservasController@getTurnos')->with(['message' => trans('app.reservas.error_creando_turno'), 'm_status' => 'error']);
        }

    }

    public function postEliminarTurno(Request $request){

        $company = \Auth::user()->company;
        $turno = Turno::where('id', $request->id)->where('company_id', $company->id)->first();

        if(!is_null($turno)){

            $turno->delete();
            return response()->json('ok', 200);

        } 

        return response()->json(['error' => trans('app.reservas.error_eliminando_turno')], 500);

    }

    public function getTurno($id){
        
        $company = \Auth::user()->company;
        $turno = Turno::where('id', $id)->where('company_id', $company->id)->first();

        if(is_null($turno)){
            return redirect()->action('Admin\ReservasController@getTurnos')->with(['message' => trans('app.reservas.turno_desconocido'), 'm_status' => 'error']);
        } 

        return view('admin/reservas/editar_turno', compact('turno'));

    }

    public function postTurno(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'plazas' => 'required|integer|min:1',
        ]);

        if($validator->fails()){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => $validator->errors()->first(), 'm_status' => 'error']);
        }

        $hora_inicio = new Carbon($request->hora_inicio);
        $hora_fin = new Carbon($request->hora_fin);

        if($hora_inicio->greaterThanOrEqualTo($hora_fin)){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => trans('app.reservas.hora_fin_anterior_inicio'), 'm_status' => 'error']);
        }

        $fecha_inicio = $request->has('fecha_inicio') && !is_null($request->fecha_inicio) ? strtotime(str_replace('/', '-', $request->fecha_inicio)) : null;
        $fecha_fin = $request->has('fecha_fin') && !is_null($request->fecha_fin) ? strtotime(str_replace('/', '-', $request->fecha_fin)) : null;

        if(!is_null($fecha_inicio) && !is_null($fecha_fin) && $fecha_fin < $fecha_inicio){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => trans('app.reservas.fecha_fin_anterior_inicio'), 'm_status' => 'error']);
        }

        $company = \Auth::user()->company;

        $turno = \App\Turno::where('company_id', $company->id)->where('nombre', trim($request->nombre))->where('id', '<>', $id)->first();
        if(!is_null($turno)){
            $request->session()->flash('status', 'validation_error');
            return redirect()->back()->with(['message' => trans('app.reservas.turno_repetido'), 'm_status' => 'error']);
        }

        $turno = Turno::where('id', $id)->where('company_id', $company->id)->first();

        if(is_null($turno)){
            return redirect()->action('Admin\ReservasController@getTurnos')->with(['message' => trans('app.reservas.turno_desconocido'), 'm_status' => 'error']);
        }
        
        $turno->nombre = trim($request->nombre);
        $turno->inicio = $hora_inicio->toTimeString();
        $turno->fin = $hora_fin->toTimeString();
        $turno->plazas = $request->plazas;
        $turno->descripcion = $request->descripcion;
        $turno->lunes = $request->has('lunes');
        $turno->martes = $request->has('martes');
        $turno->miercoles = $request->has('miercoles');
        $turno->jueves = $request->has('jueves');
        $turno->viernes = $request->has('viernes');
        $turno->sabado = $request->has('sabado');
        $turno->domingo = $request->has('domingo');
        $turno->fecha_inicio = !is_null($fecha_inicio) ? date('Y-m-d', $fecha_inicio) : null;
        $turno->fecha_fin = !is_null($fecha_fin) ? date('Y-m-d', $fecha_fin) : null;
        if($turno->save()){
            return redirect()->back()->with(['message' => trans('app.reservas.cambios_guardados'), 'm_status' => 'success']);
        } else {
            return redirect()->back()->with(['message' => trans('app.reservas.error_guardando_turno'), 'm_status' => 'error']);
        }
        
    }

    public function getBloqueosDatatable(Request $request, $id){

        $company = \Auth::user()->company;
        if(intval($id > 0)){

            $turno = Turno::find($id);
            if($company->id != $turno->company_id){
                abort(500);
            }

        } else {

            $turno = null;

        }

        $query = BloqueoTurno::where('company_id', $company->id);
        if(is_null($turno)){
            $query = $query->whereNull('turno_id');
        } else {
            $query = $query->where('turno_id', $turno->id);
        }

        $draw = intval($request->get('draw', ''));
		$start = intval($request->get('start', 0));
		$length = intval($request->get('length', 10));

		$output = new \StdClass;
		$output->draw = $draw;
		$output->recordsTotal = $query->count();
		$output->recordsFiltered = $output->recordsTotal;
		$output->data = [];

        // no hay filtros

        $output->recordsFiltered = $query->count();

		switch($request->get('order')[0]['column']){
            case '0': $query = $query->orderBy('fecha', $request->get('order')[0]['dir']); break;
        }
        
        $query = $query->skip($start)->take($length)->get();
		foreach($query as $row){

            $row->acciones = $row->id;
            $output->data[] = $row;

		}

		return Response::json($output);
    }

    public function postNuevoBloqueo(Request $request, $id){
        
        $company = \Auth::user()->company;

        if(intval($id) > 0){

            $turno = Turno::where('id', $id)->where('company_id', $company->id)->first();
            if(is_null($turno)){
                return response()->json(['error' => trans('app.reservas.error_creando_bloqueo')], 500);
            } 

        } else {
            $turno = null;
        }

        if(!$request->has('fecha') || $request->fecha == ''){
            return response()->json(['error' => trans('app.reservas.falta_fecha_bloqueo')], 500);
        }

        $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $request->fecha)));

        if(!is_null($turno)){
            $bloqueo = BloqueoTurno::where('turno_id', $turno->id)->where('fecha', $fecha)->first();
        } else {
            $bloqueo = BloqueoTurno::whereNull('turno_id')->where('fecha', $fecha)->first();
        }
        if(!is_null($bloqueo)){
            return response()->json(['error' => trans('app.reservas.ya_existe_bloqueo')], 500);
        }

        $bloqueo = BloqueoTurno::create([
            'company_id' => $company->id,
            'turno_id' => !is_null($turno) ? $turno->id : null,
            'fecha' => $fecha
        ]);

        if(is_null($bloqueo)){
            return response()->json(['error' => trans('app.reservas.error_creando_bloqueo')], 500);
        }

        return response()->json('ok', 200);

    }

    public function postEliminarBloqueo(Request $request, $id){
        
        $company = \Auth::user()->company;
        if(intval($id) > 0){

            $turno = Turno::where('id', $id)->where('company_id', $company->id)->first();
            if(is_null($turno)){
                return response()->json(['error' => trans('app.reservas.error_creando_bloqueo')], 500);
            } 
            $bloqueo = BloqueoTurno::where('id', $request->id)->where('turno_id', $turno->id)->where('company_id', $company->id)->first();

        } else {
            $bloqueo = BloqueoTurno::where('id', $request->id)->whereNull('turno_id')->where('company_id', $company->id)->first();
        }

        if(is_null($bloqueo)){
            return response()->json(['error' => trans('app.reservas.error_eliminando_bloqueo')], 500);
        }

        $bloqueo->delete();

        return response()->json('ok', 200);

    }

    public function getBloqueos(){
        return view('admin/reservas/bloqueos');
    }

    // funciones publicas

    public function getTurnosDisponibles(Request $request, $company_id){

        $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',
        ]);

        if($validator->fails()){
            abort(500);
        }

        $start = strtotime($request->start);
        $end = strtotime($request->end);

        //$manana = strtotime('+1 day', strtotime(date('Y-m-d')));
        //$start = max(strtotime($request->start), $manana);
        
        $hoy = strtotime(date('Y-m-d'));
        $start = max($start, $hoy);
        
        if($end <= $start){
            abort(500);
        }

        if($end - $start > 50 * 24 * 60 * 60){ // más de 50 dias
            abort(500);
        }

        $company = \App\Company::find($company_id);
        if(is_null($company)){
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
                        'title' => '<strong>'.$turno->nombre.'</strong><br/><small>'.substr($turno->inicio, 0, 5).' - '.substr($turno->fin, 0, 5).'<br/>'.$plazas_disponibles.' '.trans('app.reservas.plazas_disp').'</small>',
                        'title_notags' => $turno->nombre.': '.substr($turno->inicio, 0, 5).' - '.substr($turno->fin, 0, 5),
                        'start' => date('Y-m-d', $dia),
                        'end' => date('Y-m-d', $dia),
                        'inicio' => $turno->inicio,
                        'turno_id' => $turno->id,
                    ];
                }
            }

            $dia = strtotime('+1 day', $dia);
        } while($dia <= $end);

        return response()->json($eventos);

    }

    public function postReserva(Request $request){

        $user = \Auth::user();
        $company = \App\Company::find($request->company_id);
        
        if(!\Auth::check() || $user->role != 'user' || is_null($company) || $company->type != 1 || $company->enable_reservas != 1){
            abort(500);
        }
        
        $validator = Validator::make($request->all(), [
            'turno_id' => 'required',
            'fecha' => 'required',
            'plazas' => 'required|integer|min:1'
        ]);
        
        if($validator->fails()){
            return redirect()->back()->with(['message' => trans('app.reservas.falta_turno'), 'm_status' => 'error']);
        }
    
        $turno = \App\Turno::find($request->turno_id);
        if(is_null($turno) || $turno->company_id != $company->id){
            abort(500);
        }

        $fecha = strtotime($request->fecha);
        $turno->reset_cache($fecha);
        if(!$turno->fecha_disponible($fecha)){
            return redirect()->back()->with(['message' => trans('app.reservas.fecha_no_valida'), 'm_status' => 'error']);
        }

        $plazas = intval($request->plazas);
        if(!$turno->fecha_disponible($fecha, $plazas)){
            return redirect()->back()->with(['message' => trans('app.reservas.no_hay_plazas'), 'm_status' => 'error']);
        }

        $reserva = \App\Reserva::create([
            'user_id' => $user->id,
            'turno_id' => $turno->id,
            'fecha' => date('Y-m-d', $fecha),
            'plazas' => $plazas
        ]);

        if($reserva){
            dispatch(new PushCreateReservaJob($reserva));
            return redirect()->back()->with(['message' => trans('app.reservas.reserva_solicitada'), 'm_status' => 'success']);
        } else {
            return redirect()->back()->with(['message' => trans('app.reservas.error_creando_reserva'), 'm_status' => 'error']);
        }

    }

    public function postReservaManual(Request $request){

        $user = \Auth::user();
        $company = $user->company;
        
        if(!\Auth::check() || $user->role != 'admin' || is_null($company) || $company->type != 1 || $company->enable_reservas != 1){
            abort(500);
        }

        $validator = Validator::make($request->all(), [
            'turno_id' => 'required',
            'fecha' => 'required',
            'plazas' => 'required|integer|min:1',
            'nombre' => 'required',
            'telefono' => 'required',
        ]);
            
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->first()], 500);
        }
    
        $turno = \App\Turno::find($request->turno_id);
        if(is_null($turno) || $turno->company_id != $company->id){
            abort(500);
        }

        $fecha = strtotime($request->fecha);
        $turno->reset_cache($fecha);
        if(!$turno->fecha_disponible($fecha)){
            return response()->json(['error' => trans('app.reservas.fecha_no_valida')], 500);
        }

        $plazas = intval($request->plazas);
        if(!$turno->fecha_disponible($fecha, $plazas)){
            return response()->json(['error' => trans('app.reservas.no_hay_plazas')], 500);
        }

        $reserva = \App\Reserva::create([
            'user_id' => null,
            'turno_id' => $turno->id,
            'fecha' => date('Y-m-d', $fecha),
            'plazas' => $plazas,
            'nombre' => $request->nombre,
            'telefono' => !is_null($request->telefono) ? $request->telefono : '',
            'confirmado' => true,
        ]);

        if($reserva){
            return response()->json(['message' => trans('app.reservas.reserva_guardada')]);
        } else {
            return response()->json(['error' => trans('app.reservas.error_creando_reserva')], 500);
        }

    }

    public function getProximasReservas(){
        return view('admin/reservas/listado_reservas_cliente', ['filtro' => 'proximas']);
    }

    public function getReservasPasadas(){
        return view('admin/reservas/listado_reservas_cliente', ['filtro' => 'pasadas']);
    }

    public function getReservasClienteDatatables(Request $request){

        $user = \Auth::user();

        $draw = intval($request->get('draw', ''));
		$start = intval($request->get('start', 0));
		$length = intval($request->get('length', 10));

		$output = new \StdClass;
		$output->draw = $draw;
		$output->recordsTotal = 0;
		$output->recordsFiltered = 0;
        $output->data = [];

        $query = \App\Reserva::with(['turno.company'])->where('user_id', $user->id);

        $filtro = !is_null($request->filtro) ? $request->filtro : 'proximas';
        switch($filtro){
            case 'proximas':
                $query = $query->where('fecha', '>=', date('Y-m-d'));
            break;
            case 'pasadas':
                $query = $query->where('fecha', '<', date('Y-m-d'));
            break;
        }

        $output->recordsTotal = $query->count();

        if(trim($request->search['value']) != ''){
			$search = explode(' ', trim($request->search['value']));
			foreach($search as $s){
				if($s != ''){

                    $query = $query->where(function($q) use ($s){

                        return $q
                            ->whereHas('turno', function($q2) use ($s){
                                return $q2
                                    ->where('nombre', 'like', '%'.$s.'%')
                                    ->orWhere('descripcion', 'like', '%'.$s.'%');
                            })
                            ->orWhereHas('turno.company', function($q2) use ($s){
                                return $q2
                                    ->where('name', 'like', '%'.$s.'%')
                                    ->orWhere('name_comercial', 'like', '%'.$s.'%')
                                    ->orWhere('phone', 'like', '%'.$s.'%')
                                    ->orWhere('phone2', 'like', '%'.$s.'%');
                            });

                    });

				}
			}
        }

        $output->recordsFiltered = $query->count();

		switch($request->get('order')[0]['column']){
            case '0': $query = $query->orderBy('fecha', $request->get('order')[0]['dir']); break;
        }
        
        $query = $query->skip($start)->take($length)->get();
		foreach($query as $row){

            $row->horario = $row->id;
            $row->estado = $row->id;
            $row->acciones = $row->id;
            $output->data[] = $row;

		}

		return Response::json($output);

    }

    public function postAnularReserva(Request $request){

        $user = \Auth::user();
        $reserva = \App\Reserva::find($request->id);

        if($reserva->user->id != $user->id){
            return response()->json(['error' => trans('app.reservas.error_anulando_reserva')], 500);
        }

        if($reserva->anulado){
            return response()->json('ok', 200);
        }

        $reserva->confirmado = false;
        $reserva->anulado = true;

        if($reserva->save()){
            dispatch(new PushAnularReservaJob($reserva));
            return response()->json('ok', 200);
        } else {
            return response()->json(['error' => trans('app.reservas.error_anulando_reserva')], 500);
        }

    }

}