<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\CategoriaEvento;
use App\Evento;
use App\Comision;
use DataTables;
use Carbon\Carbon;
use Auth;
use Config;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use Paypal;
use Session;
use Redirect;
use Mail;
use App\Mail\EventoMail;
use App\DataTables\AdminEventosDataTable; 
use Ramsey\Uuid\Uuid;
use App\Jobs\PushNuevoEventoJob;
use App\Jobs\PushEventoValidadoJob;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class EventoController extends Controller
{
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        return view('admin.eventos.index')->with(['datatable' => 'todos']);
    }

    public function administracion(AdminEventosDataTable $datatable)
    {  

        if (Auth::user()->role == 'superadmin'){ // SOLO ADMIN  ADMINISTRA
             return $datatable->render('admin.eventos.admin');
        }
        return view('admin.eventos.index')->with(['datatable' => 'todos']);    
    }


    public function eventoDataTable(Request $request){
        $eventos = Evento::query()->with('company', 'organizador', 'categoria')
                    ->where('desde', '>=', Carbon::now());
        
        return DataTables::of($eventos)->editColumn('organizador_id', function($row){               
                return $row->organizador_id ? $row->organizador->name : '';
            }) 
            ->editColumn('nombre', function($row){               
                return '<a href="'.route('home.evento', $row->id).'">'.$row->nombre.' </a>';
            })
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name_comercial : '';
            })   
            ->editColumn('categoria_evento_id', function($row){               
                return $row->categoria_evento_id ? $row->categoria->name : '';
            })               
            ->editColumn('desde', function($row){   
                return $row->desde ? $row->desde->format('d-m-Y H:i') : '';
            })
            ->editColumn('hasta', function($row){   
                return $row->hasta ? $row->hasta->format('d-m-Y H:i') : '';
            })
            ->filterColumn('company_id', function ($query, $keyword){            
                    return $query->whereHas('company', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                    });
            })      
            ->filterColumn('organizador_id', function ($query, $keyword){            
                    return $query->whereHas('organizador', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                        $q->orWhere('surname', 'like', '%'.$keyword.'%');
                    });
            })  
            ->addColumn('actions', 'admin.eventos.datatable_actions')    
            ->rawColumns(['actions', 'nombre'])->make(true);
    }

    public function eventosDisponiblesDataTable(Request $request){
        $eventos = Evento::query()->with('company', 'organizador', 'categoria')                                        
                    ->where('desde', '>=', Carbon::now())
                    ->where('validado', 1);
        return DataTables::of($eventos)->editColumn('organizador_id', function($row){               
                return $row->organizador_id ? $row->organizador->name : '';
            }) 
            ->editColumn('nombre', function($row){               
                return '<a href="'.route('home.evento', $row->id).'">'.$row->nombre.' </a>';
            })
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name_comercial : '';
            })   
             ->filterColumn('organizador_id', function ($query, $keyword){            
                    return $query->whereHas('organizador', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                        $q->orWhere('surname', 'like', '%'.$keyword.'%');
                    });
            })      
            ->editColumn('categoria_evento_id', function($row){               
                return $row->categoria_evento_id ? $row->categoria->name : '';
            })               
            ->editColumn('desde', function($row){   
                return $row->desde ? $row->desde->format('d-m-Y H:i') : '';
            })
            ->editColumn('hasta', function($row){   
                return $row->hasta ? $row->hasta->format('d-m-Y H:i') : '';
            })
            ->filterColumn('company_id', function ($query, $keyword){            
                    return $query->whereHas('company', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                    });
            })  
            ->addColumn('actions', 'admin.eventos.datatable_actions')    
            ->rawColumns(['actions', 'nombre'])->make(true);
    }

    public function misEventos(Request $request){
         return view('admin.eventos.index')->with(['datatable' => 'misEventos']);
    }

    public function misEventosDatatable(Request $request){
        $eventos = Evento::where('organizador_id', \Auth::user()->id)->with('company','categoria', 'organizador');
        
        return DataTables::of($eventos)
           /* ->editColumn('organizador_id', function($row){               
                return $row->organizador_id ? $row->organizador->name : '';
            }) 
            ->filterColumn('organizador_id', function ($query, $keyword){            
                    return $query->whereHas('organizador', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                        $q->orWhere('surname', 'like', '%'.$keyword.'%');
                    });
            })  
                     */    
            ->editColumn('nombre', function($row){               
                return '<a href="'.route('home.evento', $row->id).'">'.$row->nombre.' </a>';
            })   
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name_comercial : '';
            })   
             ->filterColumn('organizador_id', function ($query, $keyword){            
                    return $query->whereHas('organizador', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                        $q->orWhere('surname', 'like', '%'.$keyword.'%');
                    });
            })        
            ->editColumn('desde', function($row){   
                return $row->desde ? $row->desde->format('d-m-Y H:i') : '';
            })
            ->editColumn('hasta', function($row){   
                return $row->hasta ? $row->hasta->format('d-m-Y H:i') : '';
            })
            ->addColumn('actions', 'admin.eventos.admin_eventos_datatable_actions')    
            ->rawColumns(['actions', 'nombre'])->make(true);

    }

    public function enMiNegocio(Request $request){
         return view('admin.eventos.enminegocio');
    }

    public function enMiNegocioDatatable(Request $request){
        if(\Auth::user()->role == 'superadmin'){
            $eventos = Evento::where('company_id',null)
                    ->with('categoria', 'organizador')
                    ->where('desde', '>=', Carbon::now());
    
        }else{
            $eventos = Evento::where('company_id', \Auth::user()->company->id)
                        ->with('categoria', 'organizador')
                        ->where('desde', '>=', Carbon::now());            
        }

        return DataTables::of($eventos)->editColumn('organizador_id', function($row){               
                return $row->organizador_id ? $row->organizador->name : '';
            }) 
            ->editColumn('nombre', function($row){               
                return '<a href="'.route('home.evento', $row->id).'">'.$row->nombre.' </a>';
            })
            ->filterColumn('organizador_id', function ($query, $keyword){            
                    return $query->whereHas('organizador', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                        $q->orWhere('surname', 'like', '%'.$keyword.'%');
                    });
            })  
            ->editColumn('categoria_evento_id', function($row){               
                return $row->categoria_evento_id ? $row->categoria->name : '';
            })               
            ->editColumn('desde', function($row){   
                return $row->desde ? $row->desde->format('d-m-Y H:i') : '';
            })
            ->editColumn('hasta', function($row){   
                return $row->hasta ? $row->hasta->format('d-m-Y H:i') : '';
            })
             
            ->addColumn('actions', 'admin.eventos.negocio_eventos_datatable_actions')    
            ->rawColumns(['actions', 'nombre'])->make(true);

    }

    public function asistire(Request $request){
         return view('admin.eventos.asistire')->with(['datatable' => 'asistire']);
    }

    public function asistireDatatable(Request $request){

        $eventos = \DB::table('cliente_evento')->select('cliente_evento.*', 'eventos.desde as desde_evento','eventos.nombre as nombre_evento', 'eventos.direccion as direccion_evento' ,'users.name as organizador_name','users.surname as organizador_surname' ,'companies.name as company_name')
                    ->leftJoin('eventos', 'cliente_evento.evento_id', '=', 'eventos.id')
                    ->leftJoin('users', 'eventos.organizador_id', '=', 'users.id')
                    ->leftJoin('companies', 'eventos.company_id', '=', 'companies.id')
                    ->where('cliente_id', \Auth::user()->id)
                    ->where('eventos.desde', '>=', Carbon::now()->startOfDay());
        
           
         return DataTables::of($eventos)->editColumn('organizador_id', function($row){        
                return $row->organizador_name.' '.$row->organizador_surname ;
            })   
            ->filter(function ($query) use ($request){                    
                  if($request->search['value']){
                    $keyword = $request->search['value'];
                    $query->where('eventos.desde', 'like', '%'.$keyword.'%')
                        ->orWhere('eventos.nombre', 'like', '%'.$keyword.'%')
                        ->orWhere('users.name', 'like', '%'.$keyword.'%')
                        ->orWhere('users.surname', 'like', '%'.$keyword.'%')
                        ->orWhere('companies.name', 'like', '%'.$keyword.'%');
                  }
                return  $query;
                 
            })
            
            ->editColumn('desde_evento', function($row){   
                return $row->desde_evento ? Carbon::parse($row->desde_evento)->format('d-m-Y H:i') : '';
            })
            ->addColumn('actions', 'admin.eventos.asistire_datatable_actions')    
            ->rawColumns(['actions'])->make(true);
    }

    

    


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id =null)
    {        
        
        $evento = new Evento;
        $comision = optional(Comision::where('nombre', 'eventos')->first())->porcentaje;
        $companies = Evento::AcceptedCompanies()->where('payed', 1)
                            ->where('blocked', 0)
                            ->pluck('name_comercial', 'id');
        $categorias = CategoriaEvento::all()->pluck('nombre', 'id');                    
        return view('admin.eventos.create',['evento' => $evento, 'companies' =>$companies, 'selected_company' => $id, 'comision' =>$comision, 'categorias' => $categorias ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $input = $request->all();
        $input['organizador_id'] = \Auth::user()->id;
        if(!isset($input['company_id'])){
            $input['company_id'] = null;
        }
       /* if(!$input['company_id']){
            $input['validado'] = 1;    
        }else{            
        }*/
         $input['validado'] = 0;    
        $input['desde'] = Carbon::parse($input['desde']);
        if(!$input['precio']){
            $input['precio'] = 0;
            $input['pagado_a_comercio'] = 1;
        }else{
            $input['pagado_a_comercio'] = 0; // pendiente de pago si tiene precio
        }
        if(isset($input['hasta']) && $input['hasta']){
            $input['hasta'] = Carbon::parse($input['hasta']);
            if($input['hasta']->lessThanOrEqualTo($input['desde']) ){
              $message = 'fechas incorrectas.';
                $m_status = 'error';
                return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);  
            }
        }
        
        $evento = Evento::create($input);
        if($request->imagen){
            
            $filename = $request->file('imagen')->getClientOriginalName();
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $filename = substr($filename, 0, strlen($filename) - strlen($extension) - 1);
            if(strlen($filename) > 150){
                $filename = substr($filename, 0, 150);
            }
            
            $evento
                ->addMediaFromRequest('imagen')
                ->usingName($filename)
                ->usingFileName($filename.'.'.$extension)
                ->toMediaCollection();

        }
        if($evento){
            dispatch(new PushNuevoEventoJob($evento));
        }

        $message = 'Evento creado con éxito.';
        $m_status = 'success';
        return redirect()->route('admin.eventos.mis_eventos')->with(['message' => $message, 'm_status' => $m_status]);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $evento = Evento::find($id);

        if (empty($evento)) {
            $message = 'Evento no encontrado.';
            $m_status = 'error';

           return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        if($evento->organizador_id != \Auth::user()->id && \Auth::user()->role != 'admin' && \Auth::user()->role != 'superadmin'){

            $message = 'No tiene permisos para hacer esta acción.';
            $m_status = 'error';
            return redirect()->route('admin.eventos.mis_eventos')->with(['message' => $message, 'm_status' => $m_status]);
        } 

        $comision = optional(Comision::where('nombre', 'eventos')->first())->porcentaje;
        $companies = Evento::AcceptedCompanies()->where('payed', 1)
                            ->where('blocked', 0)
                            ->pluck('name_comercial', 'id');
        $categorias = CategoriaEvento::all()->pluck('nombre', 'id');     
        $selected_company = !is_null($evento->company) ? $evento->company->id : 0;
        return view('admin.eventos.edit', ['evento' => $evento, 'companies' =>$companies, 'selected_company' => $selected_company, 'comision' =>$comision, 'categorias' => $categorias ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /** @var Evento $evento */
        $evento = Evento::find($id);

        if (empty($evento)) {
            $message = 'Evento no encontrado.';
            $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        if($evento->organizador_id != \Auth::user()->id && \Auth::user()->role != 'admin' && \Auth::user()->role != 'superadmin'){

            $message = 'No tiene permisos para hacer esta acción.';
            $m_status = 'error';
            return redirect()->route('admin.eventos.mis_eventos')->with(['message' => $message, 'm_status' => $m_status]);
        } 

        if(!$evento->es_editable && \Auth::user()->role != 'admin' && \Auth::user()->role != 'superadmin'){
            $message = 'No se puede borrar si ya hay inscritos!';
            $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        $input = $request->all();
        $input['desde'] = Carbon::parse($input['desde']);
        if(!$input['precio']){
            $input['precio'] = 0;
        }
        if(isset($input['hasta']) && $input['hasta']){
            $input['hasta'] = Carbon::parse($input['hasta']);
            if($input['hasta']->lessThanOrEqualTo($input['desde']) ){
              $message = 'fechas incorrectas.';
                $m_status = 'error';
                return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);  
            }
        }
        
        $evento->fill($input);
        $evento->save();
        if($request->imagen){
            $evento->clearMediaCollection();
            
            $filename = $request->file('imagen')->getClientOriginalName();
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $filename = substr($filename, 0, strlen($filename) - strlen($extension) - 1);
            if(strlen($filename) > 150){
                $filename = substr($filename, 0, 150);
            }
            
            $evento
                ->addMediaFromRequest('imagen')
                ->usingName($filename)
                ->usingFileName($filename.'.'.$extension)
                ->toMediaCollection();

        }
        $message = 'Evento modificado con éxito.';
        $m_status = 'success';

        if(\Auth::user()->role == 'superadmin'){
            return redirect()->route('admin.eventos.administracion')->with(['message' => $message, 'm_status' => $m_status]);    
        }
        
        return redirect()->route('admin.eventos.mis_eventos')->with(['message' => $message, 'm_status' => $m_status]);

    }

    public function eliminarImagen(Request $request, $id){

        /** @var Evento $evento */
        $evento = Evento::find($id);
        if(is_null($evento)){
            abort(500);
        }

        if($evento->organizador_id != \Auth::user()->id && \Auth::user()->role != 'admin' && \Auth::user()->role != 'superadmin'){
            abort(500);
        } 
        
        $evento->clearMediaCollection();
        return json_encode('ok');
        
    }


    public function marcarPagado($id)
    {

        if(\Auth::user()->role != 'admin' && \Auth::user()->role != 'superadmin'){
            $message = 'No tiene permisos para hacer esta acción.';
            $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        } 
        $evento = Evento::find($id);

         if (empty($evento)) {
             $message = 'Evento no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        $evento->status = 1; //Disponible
        $message = 'Evento disponible para su uso.';
        $m_status = 'success';
        
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }


    public function validarEvento(Request $request){
        $evento = Evento::where('id', $request->evento_id)->first();
        if(!$evento){
            return json_encode(['evento' => null, 'status' => '404']);
        }
        if($evento->company_id == \Auth::user()->company_id || \Auth::user()->role == 'admin' || \Auth::user()->role == 'superadmin'){
            
            $evento->validado = 1;
            $evento->save();
            $message = 'Evento disponible para su uso.';
            $m_status = 'success';

            dispatch(new PushEventoValidadoJob($evento));

        }else{
            $message = 'No tiene permisos para hacer esta acción.';
            $m_status = 'error';
        }
        
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento = Evento::find($id);

         if (empty($evento)) {
            $message = 'Evento no encontrado.';
            $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        if($evento->organizador_id == \Auth::user()->id || \Auth::user()->role == 'admin' || \Auth::user()->role == 'superadmin'){
            $message = 'Evento borrado con éxito.';
            $m_status = 'success';
            $evento->delete();
        }else{
            $message = 'No tiene permisos para hacer esta acción.';
            $m_status = 'error';
        }    
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
           
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            if($evento->organizador_id == \Auth::user()->id || \Auth::user()->role == 'admin' || \Auth::user()->role == 'superadmin'){
                try
                {
                    $evento = Evento::find($request->id);

                    $evento->delete();
                }
                catch (\Illuminate\Database\QueryException $e)
                {
                    \Log::info('DB ERROR - Deleting Regalo', ['error' => $e]);
                    
                    return 0;
                }
                catch (Exception $e)
                {
                    \Log::info('Deleting Regalo', ['error' => $e]);
                    
                    return 0;
                }
            }else{
                 \Log::info('Deleting Regalo', ['error' => 'permisos']);
                    
                return 0;
            }    
        }

        return 1;
    }

    public function exportExcel()
    {
        //Edu ver como exportan a excel lo demas
        return 1;
    }

    public function payWithpaypal($id, Request $request)
    {
        $evento = Evento::whereHas('asistentes', function ($q) use($id){
                    $q->where('cliente_evento.id', $id);
                })->first();

        if (!$evento) {
             $message = 'Evento no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        $asistencia = $evento->asistentes()->wherePivot('id', $id)->first();

        if($evento->plazas_libres < $asistencia->pivot->plazas_reservadas){
            return redirect()->route('admin.eventos.asistire')->with(['message' => 'Lo sentimos mucho pero las plazas disponibles para el evento se han agotado', 'm_status' => 'error']);
        }
            
        $precio = $asistencia->pivot->precio * $asistencia->pivot->plazas_reservadas;
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        
        $item_1 = new Item();
        $item_1->setName('Evento Busvi') /** item name **/
               ->setCurrency('EUR')
               ->setQuantity(1)
               ->setPrice($precio); /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        
        $amount = new Amount();
        $amount->setCurrency('EUR')
               ->setTotal($precio);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('Evento Busvi');
        
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('eventoPaypal.status', ['id' => $id])) /** Specify return URL **/
                      //->setCancelUrl(route('paypal.status', ['id' => $id]));
                      ->setCancelUrl(route('admin.eventos.asistire'));
        
        $payment = new Payment();
        $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));
                /** dd($payment->create($this->_api_context));exit; **/
        try
        {
            $payment->create($this->paypalApiContext());
        }
        catch (PayPal\Exception\PayPalConnectionException $ex)
        {
            return redirect()->route('admin.eventos.asistire')->with(['message' => 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo', 'm_status' => 'error']);
            //dd($ex->getCode(), $ex->getData());
        }
        catch (Exception $ex)
        {
            return redirect()->route('admin.eventos.asistire')->with(['message' => 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo', 'm_status' => 'error']);
        }

        foreach ($payment->getLinks() as $link)
        {
            if ($link->getRel() == 'approval_url')
            {
                $redirect_url = $link->getHref();
                break;
            }
        }

        /** add payment ID to session 
        session(['paypal_payment_id' => $payment->getId()]);
        **/
        
        if (isset($redirect_url))
        {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }

        return redirect()->route('admin.eventos.asistire')->with(['message' => 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo', 'm_status' => 'error']);

    }

    public function getPaymentStatus($id)
    {

        $evento = Evento::whereHas('asistentes', function ($q) use($id){
                    $q->where('cliente_evento.id', $id);
                })->first();
        if (!$evento) {
             $message = 'Evento no encontrado.';
             $m_status = 'error';

            return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
        }
        $asistire = $evento->asistentes->where('client_id', \Auth::user()->id);
        if (!$asistire) {
             $message = 'No estas inscrit@  al evento.';
             $m_status = 'error';
            return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
        }
        $asistencia = $evento->asistentes()->wherePivot('id', $id)->first();
        if ( $asistencia->pivot->pagado == 1) {
             $message = 'Evento ya pagado.';
             $m_status = 'error';
            return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
        }
       
        if (empty(Input::get('PayerID')) || empty(Input::get('token')) || empty(Input::get('paymentId')))
        {
            \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.eventos.asistire');
            //return redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
        }

        $payment_id = Input::get('paymentId');
        $api_context = $this->paypalApiContext();
        $payment = Payment::get($payment_id, $api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));
        
        /**Execute the payment **/
        try
        {
            $result = $payment->execute($execution, $api_context);
        }
        catch (PayPal\Exception\PayPalConnectionException $ex)
        {
            \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.eventos.asistire');
            //return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
        }
        catch (Exception $ex)
        {
            \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.eventos.asistire');
            //return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
        }
        
        if ($result->getState() == 'approved')
        {
            \Session::put('success', 'Pago completado');
            $message = 'Evento Disponible.';
            $m_status = 'success';
            \DB::update('update cliente_evento set pagado = 1 where id = ?', [$id]);
            //$evento->asistentes()->updateExistingPivot($id, ['pagado' => 1]);
            $evento->load('organizador');
            $evento->load('company');
            Mail::to(\Auth::user()->email)->send(new EventoMail($evento));

            return Redirect::route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            

        }
        
        \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
        return Redirect::route('admin.eventos.asistire');
        //return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);

    }


    public function paypalApiContext()
    {
        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');

        $api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        
        $api_context->setConfig($paypal_conf['settings']);

        return $api_context;
    }

    public function payWithstripe(Request $request){

        $cliente_evento_id = $request->cliente_evento_id;
        $evento = Evento::whereHas('asistentes', function ($q) use($cliente_evento_id){
                    $q->where('cliente_evento.id', $cliente_evento_id);
                })->first();

        if (!$evento) {
             $message = 'Evento no encontrado.';
             $m_status = 'error';

            if (\Request::ajax()) {
                return json_encode(['message' => $message, 'm_status' => $m_status]);
            } else {
                return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }
        }

        $asistire = $evento->asistentes->where('id', \Auth::user()->id);
        if (!$asistire) {
             $message = 'No estas inscrit@  al evento.';
             $m_status = 'error';
             if (\Request::ajax()) {
                return json_encode(['message' => $message, 'm_status' => $m_status]);
            } else {
                return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }
        }

        $asistencia = $evento->asistentes()->wherePivot('id', $cliente_evento_id)->first();
        if ( $asistencia->pivot->pagado == 1) {
             $message = 'Evento ya pagado.';
             $m_status = 'error';
             if (\Request::ajax()) {
                return json_encode(['message' => $message, 'm_status' => $m_status]);
            } else {
                return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }
        }

        if($evento->plazas_libres < $asistencia->pivot->plazas_reservadas){
            return redirect()->route('admin.eventos.asistire')->with(['message' => 'Lo sentimos mucho pero las plazas disponibles para el evento se han agotado', 'm_status' => 'error']);
        }

        \Laravel\Cashier\Cashier::useCurrency('eur');
        $user = \Auth::user();

        $amount = ($request->amount ) * 100;
        $card = $user->addCard([
            'cardNumber' => $request->card,
            'expiryMonth' => $request->month,
            'expiryYear' => $request->year,
            'cvc' => $request->cvv
        ]);

        if (! isset($card['cardId']))
        {
            if (\Request::ajax()) {
                return json_encode(['message' => $card['message'], 'm_status' => 'error']);
            } else {
                return redirect()->back()->with(['m_status' => 'danger', 'message' => $card['message']]);
            }
        }

        try {

            $user->charge($amount, [
                'cardId' => $card['cardId']
            ]);

            //TODO: Y si no paga que?

            \Session::put('success', 'Pago completado');
            $message = 'Inscrito en Evento: '.$evento->nombre ;
            $m_status = 'success';
                
            // $evento->asistentes()->updateExistingPivot($id, ['pagado' => 1]);
            \DB::update('update cliente_evento set pagado = 1 where id = ?', [$cliente_evento_id]);

            $evento->load('organizador');
            $evento->load('company');
            Mail::to(\Auth::user()->email)->send(new EventoMail($evento));

            if (\Request::ajax()) {
                return json_encode(['message' => $message, 'm_status' => $m_status]);
            } else {
                return Redirect::route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }
            
        } catch(\Exception $ex){

            $message = 'Ha ocurrido un error realizando el pago del evento: '.$evento->nombre." - ".$ex->getMessage();
            $m_status = 'error';

            if (\Request::ajax()) {
                return json_encode(['message' => $message, 'm_status' => $m_status]);
            } else {
                return Redirect::route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }
            
        }

    }

    public function marcarEventoPagado($id){
        $evento = Evento::find($id);

        if (empty($evento)) {
             $message = 'Evento no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

         if ($evento->pagado_a_comercio) {
             $message = 'Evento ya pagado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        $evento->pagado_a_comercio = 1;
        $evento->save();
        $message = 'Evento marcado pagado con éxito.';
        $m_status = 'success';
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function confirmar_asistencia($id)
    {
        $evento = Evento::find($id);
        if (empty($evento)) {
             $message = 'Evento no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        if($evento->asistentes()->where('cliente_id',\Auth::user()->id )){
            $message = 'Ya estas inscrit@.';
            $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        $evento->asistentes()->attach(\Auth::user()->id);

        return redirect()->route('admin.eventos.mis_eventos');
    }

    public function apuntarse(Request $request){

        if($request->plazas_reservadas > 0 ){
            $evento = Evento::find($request->apuntame_evento_id);
            if(!$evento){
                if (\Request::ajax()) {
                    return json_encode(['evento' => null, 'status' => '404']);
                } else {
                    return redirect()->route('admin.eventos.asistire')->with(['message' => trans('app.eventos.evento_no_disponible'), 'm_status' => 'error']);
                }
            }

            if(intval($request->plazas_reservadas) <= intval($evento->plazas_libres)){
                $id =null;
                $uuid = (string) Uuid::uuid4();
                $pagado = $evento->precio_final == 0 ? 1 : 0;            
                $evento->asistentes()->attach( \Auth::user()->id,['pagado' => $pagado, 'confirmacion_asistencia' => 0, 'plazas_reservadas' => $request->plazas_reservadas, 'ultimo_uso_entrada' => null, 'uuid' => $uuid, 'precio' => $evento->precio_final]);
                $message = 'Apuntado al evento correctamente.';
                $id = \DB::getPdo()->lastInsertId();
                $m_status = 'success';
                if (\Request::ajax()) {
                    return json_encode(['id' => $id, 'status' => '200']);
                }
                return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }else{
                $m_status = 'error';
                $message = 'No hay plazas suficientes.';
                if (\Request::ajax()) {
                    return json_encode(['id' => null, 'status' => '500']);
                }
                return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);
            }
        }    
        $m_status = 'error';
        $message = 'Al menos debe ir una persona.';
        if (\Request::ajax()) {
            return json_encode(['id' => null, 'status' => '500']);
        }
        return redirect()->route('admin.eventos.asistire')->with(['message' => $message, 'm_status' => $m_status]);


    }

    public function validar(Request $request){
        
        $evento = Evento::find($request->evento_id);
        if(!$evento){
            return json_encode(['msg' => 'Evento no encontrado', 'status' => '404']);
        }
        if($evento->company_id != optional(\Auth::user()->company)->id){
            return json_encode(['msg' => 'No tiene permisos para hacer esta acción', 'status' => '500']);
        }
        
        $evento->validado = $request->value;
        $evento->save();

        if($evento->validado){
            dispatch(new PushEventoValidadoJob($evento));
        }
        
        return json_encode(['message' => $evento, 'status' => '200']);

    }

    public function asistentesDatatable(Request $request, $id){

        $evento = Evento::find($id);
        if(is_null($evento)){
            abort(404);
        }

        if($evento->organizador_id != \Auth::user()->id && \Auth::user()->role != 'admin' && \Auth::user()->role != 'superadmin'){
            abort(404);
        }

        return FacadesDataTables::of($evento->asistentes())
            ->editColumn('pagado', function($row){
                if(intval($row->pagado) == 1){
                    return '<button type="button" class="btn btn-xs btn-success">Pagado</button>';
                } else {
                    return '<button type="button" class="btn btn-xs btn-danger">Pendiente</button>';
                }
            })
            ->editColumn('confirmacion_asistencia', function($row){
                if(intval($row->confirmacion_asistencia) == 1){
                    return '<button type="button" class="btn btn-xs btn-success">Confirmada</button>';
                } else {
                    return '<button type="button" class="btn btn-xs btn-danger">Pendiente</button>';
                }
            })
            ->rawColumns(['pagado', 'confirmacion_asistencia'])
            ->make(true);

    }

}
