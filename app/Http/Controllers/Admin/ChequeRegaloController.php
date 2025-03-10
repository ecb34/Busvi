<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\ChequeRegalo;
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
use App\Mail\ChequeMail;
use App\DataTables\AdminChequeRegaloDataTable; 

class ChequeRegaloController extends Controller
{
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        return view('admin.cheques_regalo.index');
    }

    public function administracion(AdminChequeRegaloDataTable $datatable)
    {  

        if (Auth::user()->role == 'superadmin'){ // SOLO ADMIN  ADMINISTRA
             return $datatable->render('admin.cheques_regalo.admin');
        }
        return view('admin.cheques_regalo.index');    
    }


    public function chequesRegaloDataTable(Request $request){
             
        $chequesregalo = ChequeRegalo::query()->with('company');

        if (Auth::user()->role == 'user'){
            $to_user_id = Auth::user()->id;
            $chequesregalo = $chequesregalo->where('to_user_id', $to_user_id)->where('status','>', 0 );
        }else{
            if($request->to_user_id){
                $chequesregalo =$chequesregalo->where('to_user_id', $request->to_user_id);
            }
            if($request->from_user_id){
                $chequesregalo =$chequesregalo->where('from_user_id', $request->from_user_id);
            }    
        }   
        
        return DataTables::of($chequesregalo)   
            ->editColumn('created_at', function($row){                
                return $row->created_at->format('d-m-Y');
            })  
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name_comercial : 'Cualquiera';
            })      
            ->editColumn('status', function($row){
                return Config::get('cheques_regalo.estados')[$row->status];
            })  
            ->addColumn('actions', 'admin.cheques_regalo.datatable_actions')    
            ->rawColumns(['actions'])->make(true);
    }


    public function chequesRegaloAdminDataTable(Request $request){

             
      /*  Cambiado a databale server side
       $chequesregalo = ChequeRegalo::query()->with('company');

       
        return DataTables::of($chequesregalo)   
            ->editColumn('created_at', function($row){                
                return $row->created_at->format('d-m-Y');
            })  
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name : 'Cualquiera';
            })      
            ->editColumn('status', function($row){
                return Config::get('cheques_regalo.estados')[$row->status];
            })     
             ->editColumn('pagado_a_comercio', function($row){
                return $row->pagado_a_comercio ? 'SI' : 'NO' ;
            })   
             ->editColumn('used_at', function($row){   
                return $row->used_at ? $row->used_at->format('d-m-Y') : '';
            }) 
            ->addColumn('actions', 'admin.cheques_regalo.admin_datatable_actions')    
            ->rawColumns(['actions'])->make(true);*/
    }


    public function pendientesPago()
    {  
        return view('admin.cheques_regalo.pendientes_pago');
    }


    public function chequesRegaloAPagarDataTable(Request $request){
             
        $chequesregalo = ChequeRegalo::query()->with('company');

        if (Auth::user()->role == 'user'){
            $chequesregalo = $chequesregalo->where('from_user_id', Auth::user()->id)->where('status',0);
        }
        
        return DataTables::of($chequesregalo)   
            ->editColumn('created_at', function($row){                
                return $row->created_at->format('d-m-Y');
            })  
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name_comercial : 'Cualquiera';
            })      
            ->editColumn('status', function($row){
                return Config::get('cheques_regalo.estados')[$row->status];
            })      
            ->addColumn('actions', 'admin.cheques_regalo.datatable_actions')    
            ->rawColumns(['actions'])->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id =null)
    {        
        $estados = Config::get('cheques_regalo.estados');
        $cheque_regalo = new ChequeRegalo;
        $companies = ChequeRegalo::AcceptedCompanies()->where('payed', 1)
                            ->where('blocked', 0)
                            ->pluck('name_comercial', 'id');
        return view('admin.cheques_regalo.create',['estados' => $estados, 'cheque_regalo' => $cheque_regalo, 'companies' =>$companies, 'selected_company' => $id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|email',
            'importe' => 'required|numeric|min:1',
        ]);

        $input = $request->all();

        if(!isset($input['from_user_id']) or is_null($input['from_user_id']))  {
            $input['from_user_id'] = Auth::user()->id;
        }

        if(trim($input['email']) == Auth::user()->email){
            $input['to_user_id'] = Auth::user()->id;
        }

        $input['status'] = 0; // pendiente de pago
        
        $cheque_regalo = ChequeRegalo::create($input);
        $message = 'ChequeRegalo creado con éxito.';
        $m_status = 'success';

        return redirect()->route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
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
         $cheque_regalo = ChequeRegalo::find($id);

        return view('admin.cheques_regalo.edit', ['cheque_regalo' => $cheque_regalo]);
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
        /** @var Epi $epi */
        $cheque_regalo = ChequeRegalo::find($id);

        if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        $input = $request->all();

        if(!$input['from_user_id']){
            $input['from_user_id'] = Auth::user()->id;
        }

        $cheque_regalo->fill($input);
        $cheque_regalo->save();
        $message = 'Cheque Regalo modificado con éxito.';
        $m_status = 'success';
        
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }


    public function marcarPagado($id)
    {
        $cheque_regalo = ChequeRegalo::find($id);

         if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
        $cheque_regalo->status = 1; //Disponible
        $message = 'Cheque Regalo disponible para su uso.';
        $m_status = 'success';

        if($cheque_regalo->from_user_id != $chequesregalo->to_user_id){
            $chequesregalo->load('emisor');
            Mail::to($cheque_regalo->email)->send(new ChequeMail($cheque_regalo));
        }
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }


    public function aceptarCheque(Request $request){
        $cheque_regalo = ChequeRegalo::where('uuid', $request->uuid)->first();
        if(!$cheque_regalo){
            return json_encode(['cheque' => null, 'status' => '404']);
        }
        $cheque_regalo->to_user_id = \Auth::user()->id;
        $cheque_regalo->save();
        $message = 'Cheque Regalo disponible para su uso.';
        $m_status = 'success';
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
        $cheque_regalo = ChequeRegalo::find($id);

         if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        $message = 'Cheque Regalo borrado con éxito.';
        $m_status = 'success';
        $cheque_regalo->delete();
        return redirect()->route('rates.index')->with(['message' => $message, 'm_status' => $m_status]);
        
        
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $cheque_regalo = ChequeRegalo::find($request->id);

                $cheque_regalo->delete();
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

        $cheque_regalo = ChequeRegalo::find($id);

        if($cheque_regalo->importe < 1){
            $message = 'El importe del cheque regalo no es válido';
            $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        
        $item_1 = new Item();
        $item_1->setName('Cheque Regalo Busvi') /** item name **/
               ->setCurrency('EUR')
               ->setQuantity(1)
               ->setPrice($cheque_regalo->importe); /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        
        $amount = new Amount();
        $amount->setCurrency('EUR')
               ->setTotal($cheque_regalo->importe);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('Cheque Regalo Busvi');
        
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('chequeRegaloPaypal.status', ['id' => $cheque_regalo->id])) /** Specify return URL **/
                      //->setCancelUrl(route('paypal.status', ['id' => $id]));
                      ->setCancelUrl(route('admin.cheques_regalo.pendientes_pago'));
        
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
            \Session::put('error', 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.cheques_regalo.pendientes_pago');
            //dd($ex->getCode(), $ex->getData());
        }
        catch (Exception $ex)
        {
            \Session::put('error', 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.cheques_regalo.pendientes_pago');
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

        \Session::put('error', 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo');
        return Redirect::route('admin.cheques_regalo.pendientes_pago');

    }

    public function getPaymentStatus($id)
    {

        $cheque_regalo = ChequeRegalo::find($id);

        if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';

            return redirect()->route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
        }

         if ($cheque_regalo->status > 0) {
             $message = 'Cheque Regalo ya pagado.';
             $m_status = 'error';

            return redirect()->route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
        }
        /** Get the payment ID before session clear */

        

        if (empty(Input::get('PayerID')) || empty(Input::get('token')) || empty(Input::get('paymentId')))
        {
            \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.cheques_regalo.pendientes_pago');
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
            return Redirect::route('admin.cheques_regalo.pendientes_pago');
            //return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
        }
        catch (Exception $ex)
        {
            \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
            return Redirect::route('admin.cheques_regalo.pendientes_pago');
            //return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
        }
        
        if ($result->getState() == 'approved')
        {
            \Session::put('success', 'Pago completado');
            $message = 'Cheque Regalo Disponible.';
            $m_status = 'success';

            $cheque_regalo->status = 1;
            $cheque_regalo->save();
            $cheque_regalo->load('emisor');
            Mail::to($cheque_regalo->email)->send(new ChequeMail($cheque_regalo));

            return Redirect::route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
            
            //return redirect()->route('paypal.response')->with(['m_status' => 'success', 'message' => 'Licencia Adquirida']);

        }
        
        \Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
        return Redirect::route('admin.cheques_regalo.pendientes_pago');
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

        $cheque_regalo = ChequeRegalo::find($request->cheque_id);

        if($cheque_regalo->importe < 1){
            $message = 'El importe del cheque regalo no es válido';
            $m_status = 'error';
            return redirect()->route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
        }

        if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';
            return redirect()->route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
        }

         if ($cheque_regalo->status > 0) {
             $message = 'Cheque Regalo ya pagado.';
             $m_status = 'error';

            return redirect()->route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
        }

        \Laravel\Cashier\Cashier::useCurrency('eur');
        $user = \Auth::user();

        $amount = $cheque_regalo->importe * 100;
        $card = $user->addCard([
            'cardNumber' => $request->card,
            'expiryMonth' => $request->month,
            'expiryYear' => $request->year,
            'cvc' => $request->cvv
        ]);

        if (! isset($card['cardId']))
        {
            return redirect()->back()->with(['m_status' => 'danger', 'message' => $card['message']]);
        }

        $user->charge($amount, [
             'cardId' => $card['cardId']
        ]);

        //TODO: Y si no paga que?

        \Session::put('success', 'Pago completado');
            $message = 'Cheque Regalo Disponible.';
            $m_status = 'success';

            $cheque_regalo->status = 1;
            $cheque_regalo->save();
            $cheque_regalo->load('emisor');
            Mail::to($cheque_regalo->email)->send(new ChequeMail($cheque_regalo));

            return Redirect::route('admin.cheques_regalo.pendientes_pago')->with(['message' => $message, 'm_status' => $m_status]);
            



    }

    public function marcarChequeRegaloPagado($id){
        $cheque_regalo = ChequeRegalo::find($id);
        if (empty($cheque_regalo)) {
             $message = 'Cheque Regalo no encontrado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

         if ($cheque_regalo->pagado_a_comercio) {
             $message = 'Cheque Regalo ya pagado.';
             $m_status = 'error';

            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }

        $cheque_regalo->pagado_a_comercio = 1;
        $cheque_regalo->save();
        $message = 'Cheque Regalo marcado pagado con éxito.';
        $m_status = 'success';
        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }


    public function deNegocio(AdminChequeRegaloDataTable $datatable)
    {  
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'crew'){ 
             return $datatable->with(['tipo' => 'negocio'])->render('admin.cheques_regalo.admin');
        }
        else{
             $message = 'No tiene permiso para entrar aqui.';
             $m_status = 'error';
            return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
        }
    }

   

    

}
