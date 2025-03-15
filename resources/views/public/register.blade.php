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
                <h1>{{ trans('app.public.user_register') }}</h1>
            </div>
            <!-- Contact Form -->
            <div class="register-form text-center col-lg-12 col-md-12 col-xs-12">
                {!! Form::open(['route' => 'home.make_register', 'method' => 'POST']) !!}
                    {!! Form::hidden('type', 'user') !!}

                    <div class="input-two space-80">
                        <div class="input-box">
                            {!! Form::label('name', 'Nombre: *') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                        <div class="input-box">
                            {!! Form::label('email', 'Email: *') !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="input-two space-80">
                        <div class="input-box">
                            {!! Form::label('password', 'Contraseña: *') !!}
                            {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
	                        <p class="text-info">
	                        	<small>
	                        		5 carácteres como mínimo
	                        	</small>
	                        </p>
                        </div>
                        <div class="input-box">
                            {!! Form::label('confirm_password', 'Confirmar contraseña: *') !!}
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

                    <div class="form-group col-sm-12">
                        <div class="text-center">
                            <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha_key') }}" style="display: inline-block; margin-bottom: 20px;"></div>
                        </div>
                        <button class="button orange icon" type="submit">
                            {{ trans('app.common.register') }} <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
	    @if ($errors->any())
	        @foreach ($errors->all() as $error)
		        swal({
		            type: "error",
		            title: "{{ $error }}"
		        });
	        @endforeach
	    @else
		    @if (session('message'))
		        swal({
		            type: "{{ session('m_status') }}",
		            title: "{{ session('message') }}"
		        });
		    @endif
		@endif

        $(function () {
            $('#datetimepicker1').datetimepicker({
                locale: 'es',
                format: 'DD-MM-YYYY'
            });

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
        });
    </script>
@endsection
