@extends('layouts.web')

@section('content')
<!-- Breadcrumbs
============================================ -->
<div class="page-title-social margin-0">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title float-left">
                    <h2>{{ trans('app.public.type_register') }}</h2>
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
                <h1>{{ trans('app.public.company_register') }}</h1>
            </div>
            <!-- Contact Form -->
            <div class="register-form text-center col-xs-12 col-sm-12">
                <div class="row">
                {!! Form::open(['route' => 'home.make_register', 'method' => 'POST', 'id' => 'companiesForm']) !!}
                    {!! Form::hidden('type', 'admin') !!}
                    <div class="col-xs-12 col-sm-8">
                        <h4 class="m-b-35 text-left">{{ trans('app.public.company_data') }}</h4>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <h4 class="m-b-35 text-left">{{ trans('app.public.company_data_admin') }}</h4>
                    </div>
                    <div class="col-xs-12 col-md-4 bg-info p-b-20 p-t-20">
                        <!-- Name Field -->
                        <div class="input-box">
                            {!! Form::label('name_comercial', trans('app.common.comercial_name') . ': *') !!}
                            {!! Form::text('name_comercial', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Name Field -->
                        <div class="input-box">
                            {!! Form::label('name', trans('app.common.fiscal_name') . ': *') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- CIF Field -->
                        <div class="input-box">
                            {!! Form::label('cif', trans('app.common.cif') . ': *') !!}
                            {!! Form::text('cif', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Provincia Field -->
                        <div class="input-box">
                            {!! Form::label('province', trans('app.common.province') . ': *') !!}
                            {!! Form::select('province', $provinces, null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- CP Field -->
                        <div class="input-box">
                            {!! Form::label('cp', trans('app.common.cp') . ': *') !!}
                            {!! Form::text('cp', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4 bg-info p-b-20 p-t-20">

                        <!-- City Field -->
                        <div class="input-box">
                            {!! Form::label('city', trans('app.common.city') . ': *') !!}
                            {!! Form::text('city', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Address Field -->
                        <div class="input-box">
                            {!! Form::label('address', trans('app.common.address') . ': *') !!}
                            {!! Form::text('address', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Phone Field -->
                        <div class="input-box">
                            {!! Form::label('phone', trans('app.common.phone') . ': *') !!}
                            {!! Form::text('phone', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Phone2 Field -->
                        <div class="input-box">
                            {!! Form::label('phone2', trans('app.common.phone') . ' 2:') !!}
                            {!! Form::text('phone2', null, ['class' => 'form-control']) !!}
                        </div>

                        <!-- Sector Field -->
                        <div class="input-box">
                            {!! Form::label('sector_id', trans('app.common.sector') . ': *') !!}
                            {!! Form::select('sector_id', $sectors, null, ['class' => 'form-control', 'placeholder' => 'Escoja sector...', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-4 bg-success p-b-20 p-t-20">
                        <!-- Name Field -->
                        <div class="input-box col-sm-12">
                            {!! Form::label('name', trans('app.common.name') . ': *') !!}
                            {!! Form::text('user_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Username Field -->
                        <div class="input-box col-sm-6">
                            {!! Form::label('username', trans('app.common.username') . ': *') !!}
                            {!! Form::text('username', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Email Field -->
                        <div class="input-box col-sm-6">
                            {!! Form::label('email', trans('app.common.email') . ': *') !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <!-- Password Field -->
                        <div class="input-box col-sm-6">
                            {!! Form::label('password', trans('app.common.password') . ': *') !!}
                            {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
	                        <p class="text-info pos-absolute">
	                        	<small>
	                        		5 carácteres como mínimo
	                        	</small>
	                        </p>
                        </div>

                        <!-- Password Field -->
                        <div class="input-box col-sm-6">
                            {!! Form::label('confirm_password', trans('app.common.confirm_password') . ': *') !!}
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('accept', 'accept', false, ['required' => 'required']); !!} Acepto los <a href="{{ url('terminos-y-condiciones') }}" class="text-primary" target="_blank">Términos y condicinones</a>
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-sm-12 m-t-35">

                        <div class="text-center">
                            <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha_key') }}" style="display: inline-block; margin-bottom: 20px;"></div>
                        </div>
                        <button class="button orange icon" type="submit">
                            {{ trans('app.common.register') }} <i class="fa fa-angle-right"></i>
                        </button>
                        
                        {{-- <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{ config('services.stripe.key') }}"
                            data-amount="1000"
                            data-name="Demo Book"
                            data-description="This is good start up book."
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-locale="auto">
                        </script> --}}
                    </div>
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('lib/iban.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        @if (session('message'))
            swal({
                type: "{{ session('m_status') }}",
                title: "{{ session('message') }}"
            });
        @endif

        // $('input[name="accept"]').on('change', function () {
        //     if (! $(this).prop('checked'))
        //     {
        //         $('button[type="submit"]').prop('disabled', true);
        //         $('button[type="submit"]').css('opacity', 0.6);
        //     }
        //     else
        //     {
        //         $('button[type="submit"]').prop('disabled', false);
        //         $('button[type="submit"]').css('opacity', 1);
        //     }
        // });

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

        $('input[name="bank_count"]').on('keyup change', function (e) {console.log('ghfhgfh')
            if (IBAN.isValid($(this).val()) || $(this).val() == '')
            {
                $('#companiesForm').find('button[type="submit"]').prop('disabled', false);
                $(this).css('box-shadow', 'inherit');
            }
            else
            {
                $('#companiesForm').find('button[type="submit"]').prop('disabled', true);
                $(this).css('box-shadow', '0 0 10px red');
            }
        });
    </script>
@endsection