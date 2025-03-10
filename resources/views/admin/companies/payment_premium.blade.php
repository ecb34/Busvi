@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Adquisición de Licencia</h3>
    </section>
    <div class="content">
        <div class="row text-center">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-3 m-b-15 border">
                        <table class="table table-striped table-hover table-bordered">
                            <tbody>
                                <tr>
                                	<td>
                                		Base de datos para Productos
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
	                            </tr>
                                <tr>
                                	<td>
                                		Base de datos para Servicios
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                	<td>
                                		Calendario completo de Citas
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                	<td>
                                		Cheque regalo
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                	<td>
                                		Control horario
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                	<td>
                                		Creación de eventos
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
	                            </tr>
                                <tr>
                                	<td>
                                		Galería de imágenes exposición
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                	<td>
                                		Geolocalización en tiempo real
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
	                            </tr>
                                <tr>
                                	<td>
                                		Sistema de reservas
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                	<td>
                                		Tarjeta Virtual de Presentación
                                		<span class="text-success">
                                			<i class="fa fa-check" aria-hidden="true"></i>
                                		</span>
	                                </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button class="btn btn-success btn-lg">
                                            <?php $with_tax = $premium->amount * 1.21; ?>
                                            {{ $premium->name }} {{ $premium->amount }} € + IVA ({{ number_format($with_tax, 2, '.', '') }} €)
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row text-center">
            <div class="col-xs-6 col-sm-offset-3 col-sm-3">
                <img src="{{ asset('img/paypal.png') }}" alt="PayPal" width="150px">
                <br>
                <a href="#" class="payment_method_link btn btn-primary btn-lg btn-paypal">
                    Pagar con Paypal
                </a>
            </div>

            <div class="col-xs-6 col-sm-3">
                <img src="{{ asset('img/stripe.png') }}" alt="PayPal" width="150px">
                <br>
                <a href="#" id="btnAnual" class="payment_method_link btn btn-primary btn-lg" data-toggle="modal" data-target="#modalStripeDays"">
                    Pagar con Tarjeta
                </a>
            </div>
        </div>

        <div class="hidden">
            <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form"  action="{{ route('paywithpaypal', ['id' => $company->id]) }}">
                <input type="hidden" name="amount" value="{{ $with_tax }}">
                {{ csrf_field() }}
                <?php /*
                <h2 class="w3-text-blue">Payment Form</h2>
                <p>Demo PayPal form - Integrating paypal in laravel</p>
                <p>      
                <label class="w3-text-blue"><b>Enter Amount</b></label>
                {{-- <input class="w3-input w3-border" name="amount" type="text"> --}}</p>      
                */ ?>
                <button class="w3-btn w3-blue">Pay with PayPal</button></p>
            </form>
        </div>

    </div>

@endsection

@section('modals')
    @include('admin.companies.parts.modal_stripe')
@endsection

@section('scripts')
    <script src="{{ asset('lib/simple-credit-card-validation-form/assets/js/jquery.payform.min.js') }}"></script>
    <script src="{{ asset('lib/simple-credit-card-validation-form/assets/js/script.js') }}"></script>
    <script type="text/javascript">
        window.price = {{ $with_tax }};

        $('.btn-paypal').on('click', function (e) {
            e.preventDefault();

            $('#payment-form').submit();
        });
    </script>
@endsection