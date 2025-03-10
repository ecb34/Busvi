@extends('layouts.web')

@section('content')
<!-- Breadcrumbs
============================================ -->
<div class="page-title-social margin-0">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title float-left">
                    <h2>Tipo de Registro</h2>
                </div>
            </div>
        </div>
    </div> 
</div>
<!-- Business Tab Area
============================================ -->
<div class="login-page margin-100">
    <div class="container">
        <div class="row">
            <!-- Title & Search -->
            <div class="section-title text-center col-xs-12 margin-bottom-50">
                <h1>Registro de Negocio</h1>
            </div>
            <!-- Contact Form -->
            <div class="register-form text-center col-xs-12 col-sm-12">

                <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form"  action="{{ route('paywithpaypal', ['id' => $company->id]) }}">
                    <input type="number" name="amount" value="20">
                    {{ csrf_field() }}
                    <h2 class="w3-text-blue">Payment Form</h2>
                    <p>Demo PayPal form - Integrating paypal in laravel</p>
                    <p>      
                    <label class="w3-text-blue"><b>Enter Amount</b></label>
                    <input class="w3-input w3-border" name="amount" type="text"></p>      
                    <button class="w3-btn w3-blue">Pay with PayPal</button></p>
                </form>

                <form id="paymentFormStripe" action="{{ route('paypal.status') }}" method="POST">
                    <script src="https://checkout.stripe.com/checkout.js"></script>

                    <input type="hidden" name="token" value="">
                    <input type="hidden" name="plan" value="">

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
                                    <div class="price-row"><i class="fa fa-users" aria-hidden="true"></i> Sistema de Gestión de Clientes</div>
                                    <div class="price-row">
                                        <button id="btnAnual" class="btn btn-info">Suscipción Anual</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
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
                                name: 'Freshware',
                                description: 'Suscripción Anual',
                                // zipCode: true,
                                currency: 'eur',
                                amount: 10000,
                                email: "fran.perez@microvalencia.es",
                                allowRememberMe: false
                            });
                            e.preventDefault();
                        });

                        // Close Checkout on page navigation:
                        window.addEventListener('popstate', function() {
                            handler.close();
                        });
                    </script>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
    @include('admin.companies.parts.modal_schedule_days')
@endsection

@section('scripts')
    <script type="text/javascript">
        $('input[name^="horario_"').datetimepicker({
            format: 'HH:mm'
        });

        $('.btn-modal-schedule').on('click', function (e) {
            e.preventDefault();

            $('#modalScheduleDays').modal('show');
        });

        $('.btn-copy').on('click', function (e) {
            var first_row = $('.schedule_days_inputs').find('.row').first().find('.col-xs-3');
            var ini1 = first_row.find('input[name="horario_ini1[l]"]').val();
            var fin1 = first_row.find('input[name="horario_fin1[l]"]').val();
            var ini2 = first_row.find('input[name="horario_ini2[l]"]').val();
            var fin2 = first_row.find('input[name="horario_fin2[l]"]').val();

            $('.schedule_days_inputs').find('.row').each(function () {
                $(this).find('.col-xs-3').first().find('input').val(ini1);
                $(this).find('.col-xs-3').eq(1).find('input').val(fin1);
                $(this).find('.col-xs-3').eq(2).find('input').val(ini2);
                $(this).find('.col-xs-3').last().find('input').val(fin2);
            })
        });
    </script>
@endsection