<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Deal;
use App\Status;
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
use App\Company;
use Auth;

class PaymentController extends Controller
{
	private $_api_context;

	public function __construct()
    {
    	/** PayPal api context **/
        $paypal_conf = \Config::get('paypal');

        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        
        $this->_api_context->setConfig($paypal_conf['settings']);
	}

	public function payWithpaypal($id, Request $request)
    {
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');
		
		$item_1 = new Item();
		$item_1->setName('Suscripción Busvi') /** item name **/
	           ->setCurrency('EUR')
	           ->setQuantity(1)
	           ->setPrice($request->get('amount')); /** unit price **/

		$item_list = new ItemList();
		$item_list->setItems(array($item_1));
		
		$amount = new Amount();
		$amount->setCurrency('EUR')
			   ->setTotal($request->get('amount'));

		$transaction = new Transaction();
		$transaction->setAmount($amount)
		            ->setItemList($item_list)
		            ->setDescription('Suscripción Busvi');
		
		$redirect_urls = new RedirectUrls();
		$redirect_urls->setReturnUrl(route('paypal.status', ['id' => $id])) /** Specify return URL **/
					  //->setCancelUrl(route('paypal.status', ['id' => $id]));
					  ->setCancelUrl(route('home'));
		
		$payment = new Payment();
		$payment->setIntent('Sale')
	            ->setPayer($payer)
	            ->setRedirectUrls($redirect_urls)
	            ->setTransactions(array($transaction));
		        /** dd($payment->create($this->_api_context));exit; **/
		try
		{
			$payment->create($this->_api_context);
		}
		catch (PayPal\Exception\PayPalConnectionException $ex)
		{
			\Session::put('error', 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo');
			return Redirect::route('home.index');
			//dd($ex->getCode(), $ex->getData());
		}
		catch (Exception $ex)
		{
		    \Session::put('error', 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo');
			return Redirect::route('home.index');
		}

		foreach ($payment->getLinks() as $link)
		{
			if ($link->getRel() == 'approval_url')
			{
				$redirect_url = $link->getHref();
				break;
			}
		}

		/** add payment ID to session **/
		session(['paypal_payment_id' => $payment->getId()]);

		$type = 'basic';

		if ($request->get('amount') >= 300)
		{
			$type = 'premium';
		}

		session(['type' => $type]);
		
		if (isset($redirect_url))
		{
			/** redirect to paypal **/
			return Redirect::away($redirect_url);
		}

		\Session::put('error', 'Ha ocurrido un error conectando con PayPal, por favor inténtalo de nuevo');
		return Redirect::route('home.index');

	}

	public function getPaymentStatus($id)
    {
        /** Get the payment ID before session clear **/
		$payment_id = session('paypal_payment_id');

		/** clear the session payment ID **/
		Session::forget('paypal_payment_id');

		if (empty(Input::get('PayerID')) || empty(Input::get('token')))
		{
			\Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
			return Redirect::route('home.index');
			//return redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
		}

		$payment = Payment::get($payment_id, $this->_api_context);

		$execution = new PaymentExecution();
		$execution->setPayerId(Input::get('PayerID'));
		
		/**Execute the payment **/
		try
		{
			$result = $payment->execute($execution, $this->_api_context);
		}
		catch (PayPal\Exception\PayPalConnectionException $ex)
		{
			\Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
			return Redirect::route('home.index');
			//return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
		}
		catch (Exception $ex)
		{
			\Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
			return Redirect::route('home.index');
			//return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);
		}
		
		if ($result->getState() == 'approved')
		{
			\Session::put('success', 'Pago completado, licencia adquirida');

			$company = Company::find($id);

			$company->payed = 1;
			$company->type = (session('type') == 'basic') ? 0 : 1;
			$company->enable_events = (session('type') == 'basic') ? 0 : 1;

			$company->save();

			return Redirect::route('home');
			
			//return redirect()->route('paypal.response')->with(['m_status' => 'success', 'message' => 'Licencia Adquirida']);

		}
		
		\Session::put('error', 'Ha ocurrido un error procesando el pago con PayPal, por favor inténtalo de nuevo');
		return Redirect::route('home.index');
		//return redirect()->redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);

	}

	public function StripeStatus($id, Request $request)
	{
		\Laravel\Cashier\Cashier::useCurrency('eur');

		$company = Company::find($id);
		$user = $company->admin;
    	
    	$plan = session('type');

    	// $stripe_token = $request->token;

    	$amount = $request->amount * 100;

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
    	// $user->charge($amount);

		$company->payed = 1;
		$company->type = (session('type') == 'basic') ? 0 : 1;
		$company->enable_events = (session('type') == 'basic') ? 0 : 1;

		$company->save();

		session()->forget('type');

	    return redirect()->route('companies.edit', $company->id)->with(['m_status' => 'success', 'message' => 'Licencia Adquirida']);
	}

	public function ajaxTypeVariableSession(Request $request)
	{
		if ($request->ajax())
		{
			$type = 'basic';

			if ($request->amount >= 300)
			{
				$type = 'premium';
			}

			session(['type' => $type]);
		}

		return session('type');
	}
}
