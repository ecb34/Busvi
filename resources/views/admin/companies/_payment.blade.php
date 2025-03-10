@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Adquisición de Licencia</h3>
    </section>
    <div class="content">
        <div class="row text-center">
            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-3 m-b-15 border">
                        <table class="table table-striped table-hover table-bordered">
                            <tbody>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr>
                                    <td>
                                        <button class="btn btn-success btn-lg">
                                            Premium 330 € + IVA
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-3 m-b-15 border">
                        <table class="table table-striped table-hover table-bordered">
                            <tbody>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr><td>asasddasda</td></tr>
                                <tr>
                                    <td>
                                        <button class="btn btn-danger btn-lg">
                                            Basic 85 € + IVA
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 text-center">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-success active">
                        <input type="radio" name="options" id="option1" autocomplete="off" value="330" checked> Premium 330 € + IVA
                    </label>
                    <label class="btn btn-danger">
                        <input type="radio" name="options" id="option2" autocomplete="off" value="85"> Premium 85 € + IVA
                    </label>
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
                <a href="#" id="btnAnual" class="payment_method_link btn btn-primary btn-lg">
                    Pagar con Tarjeta
                </a>
            </div>
        </div>

        <div class="hidden">
            <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form"  action="{{ route('paywithpaypal', ['id' => $company->id]) }}">
                <input type="hidden" name="amount" value="330">
                {{ csrf_field() }}
                <h2 class="w3-text-blue">Payment Form</h2>
                <p>Demo PayPal form - Integrating paypal in laravel</p>
                <p>      
                <label class="w3-text-blue"><b>Enter Amount</b></label>
                {{-- <input class="w3-input w3-border" name="amount" type="text"> --}}</p>      
                <button class="w3-btn w3-blue">Pay with PayPal</button></p>
            </form>

            <form id="paymentFormStripe" action="{{ route('stripe.status', ['id' => $company->id]) }}" method="POST">
                {{ csrf_field() }}
                <script src="https://checkout.stripe.com/checkout.js"></script>

                <input type="hidden" name="token" value="">
                <input type="hidden" name="plan" value="">
                <input type="hidden" name="amount" value="330">

                <div class="col-xs-12 col-sm-4 no-padding">
                    <div class="pricing-box featured-plan">
                        <div class="pricing-body">
                            <div class="pricing-header">
                                <h4 class="price-lable text-white bg-danger"> Mejor Opción</h4>
                                <h4 class="text-center">Anual</h4>
                                <h2 class="text-center">100<span class="price-sign">€</span></h2>
                                <p class="uppercase">al año</p>
                            </div>
                            <div class="price-table-content">
                                <div class="price-row bg-info color-white-force"><i class="fa fa-gift" aria-hidden="true"></i> 2 meses gratis</div>
                                <div class="price-row"><i class="fa fa-file-code-o" aria-hidden="true"></i> Sin límite de prespuestos</div>
                                <div class="price-row"><i class="fa fa-tablet" aria-hidden="true"></i> Visualización móvil / tablet</div>
                                <div class="price-row"><i class="fa fa-desktop" aria-hidden="true"></i> Visualización ordenador</div>
                                <div class="price-row"><i class="fa fa-users" aria-hidden="true"></i> Sistema de Gestión de Usuarios</div>
                                <div class="price-row">
                                    {{-- <button id="btnAnual" class="btn btn-info">Suscipción Anual</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('label.btn').on('click', function () {
            $('label.btn').removeClass('active');
            
            $(this).addClass('active');
            window.price = $('input[name="options"]').is(':checked').val();
        });

        $('input[name="options"]').on('change', function () {
            window.price = $('input[name="options"]:checked').val();
            $('input[name="amount"]').val(window.price);
            console.log(window.price)
            ajaxGetTypeVariableSession();
        });

        $(window).on('load', function () {
            window.price = $('input[name="options"]:checked').val();
            
            $('#payment-form').val(window.price);

            ajaxGetTypeVariableSession();
        });

        $('.btn-paypal').on('click', function (e) {
            e.preventDefault();

            $('#payment-form').submit();
        });


        var handler = StripeCheckout.configure({
            key: "{{ config('services.stripe.key') }}",
            image: "https://stripe.com/img/documentation/checkout/marketplace.png",
            locale: 'auto',
            token: function(token) {
                // You can access the token ID with `token.id`.
                // Get the token ID to your server-side code for use.
                $('input[name="token"]').val(token.id);
                $('#paymentFormStripe').submit();
            }
        });

        document.getElementById('btnAnual').addEventListener('click', function(e) {
            $('input[name="plan"]').val('anual');
            $('#paymentFormStripe').find('input[name="plan"]').val('anual')
            // Open Checkout with further options:
            handler.open({
                name: '{{ $company->name_comercial }}',
                description: 'Adquisición de Licencia',
                // zipCode: true,
                currency: 'eur',
                amount: (window.price * 100),
                email: "{{ $company->admin->email }}",
                allowRememberMe: false
            });
            e.preventDefault();
        });

        // Close Checkout on page navigation:
        window.addEventListener('popstate', function() {
            handler.close();
        });

        function ajaxGetTypeVariableSession()
        {
            console.log('ajax call: ' + window.price)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                method: "POST",
                url: "{{ route('ajaxTypeVariableSession') }}",
                data: {amount: window.price},
                success: function (response) {
                    console.log(response)
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }
    </script>
@endsection