<?php

// ESTE CONTROLADOR AHORA NO SE ESTÁ USANDO PORQUE ES
// PARA PAYPAL EXPRESS. SI EN ALGÚN MOMENTO HACE FALTA
// YA ESTA MONTADO.

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Srmklive\PayPal\Services\ExpressCheckout;

class PaypalController extends Controller
{
    protected $provider;

    public function __construct()
    {
    	$this->provider = new ExpressCheckout();
    }

    public function expressCheckout(Request $request)
    {
    	$invoice_id = time();

    	$cart = [
            // El pago no es recurrente, así que tenemos un carro
            // con nombre, precio y cantidad. En nuestro caso sólo
            // un item, que es el "alta de licencia" del tipo que
            // quieran: Básica o Premium.
            'items' => [
                [
                    'name' => 'Product 1',
                    'price' => 10,
                    'qty' => 1,
                ],
                [
                    'name' => 'Product 2',
                    'price' => 5,
                    'qty' => 2,
                ],
            ],

            // return url es la url donde Paypal vuelve después de que el usuario confirme el pago
            'return_url' => url('/paypal/' . $request->id . '/express-checkout-success'),

            // cada id de factura debe de ser único, sino tendrás un error en Paypal. Cómo no generamos
            // facturas, pasaremos el timestamp de Unix
            'invoice_id' => $invoice_id, // config('paypal.invoice_prefix') . '_' . $invoice_id,
            
            'invoice_description' => "Order #" . $invoice_id . " Invoice",
            
            // La url a la que devuelve al cancelar
            'cancel_url' => url('/'),

            // Nuestro total será el mismo que el coste de la licencia. Pero por ahora ponemos el total prueba.
            'total' => 20,
        ];

        // Añadimos el carrito a la sessión para poder
        // llamarlo desde $this->expressCheckoutSuccess
        session(['cart' => $cart]);

        // Enviamos una petición a Paypal
        // Paypal debe de responder con un array de datos
        // El array debe contener un link al sistema de pago de Paypal
        $response = $this->provider->setExpressCheckout($cart, false);

        // Si no hay link de vuelta con un mensaje de error
        if (! $response['paypal_link'])
        {
        	return redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Algo ha ido mal con Paypal']);

        	// Para ver este mensaje de error deberíamos hacer dd de $response
        }

        // Redireccionamos a Paypal
        // Después de que el pago se realice
        // iremos a $this->expressCheckoutSuccess
        return redirect($response['paypal_link']);
    }

    public function expressCheckoutSuccess($id, Request $request)
    {
    	// Obtenemos el token
    	$token = $request->get('token');

    	// Obtenemos el id del pagador
    	$PayerID = $request->get('Payer');

    	// Inicialmente Paypal redirige con un token
    	// peron no provee ninguna información adicional
    	// así que usamos getExpressCheckoutDetails($token)
    	// para obtener la información del pago
    	$response = $this->provider->getExpressCheckoutDetails($token);

    	// Si la respuesta ACK no es SUCCESS o SUCCESSWITHWARNING
    	// redirigimos con error
    	if (! in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING']))
    	{
    		return redirect()->route('paypal.response')->with(['m_status' => 'danger', 'message' => 'Error procesando el pago de Paypal']);
    	}

    	return redirect()->route('paypal.response')->with(['m_status' => 'success', 'message' => 'Licencia Adquirida']);
    }
}
