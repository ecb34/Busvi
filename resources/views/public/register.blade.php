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
                            {!! Form::label('surname', 'Apellidos: *') !!}
                            {!! Form::text('surname', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="input-two space-80">
                        <div class="input-box">
                            {!! Form::label('username', 'Username: *') !!}
                            {!! Form::text('username', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                        <div class="input-box">
                            {!! Form::label('address', 'Dirección:') !!}
                            {!! Form::text('address', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="input-two space-80 overflow-visible">
                        <div class="input-box">
                            {!! Form::label('birthday', 'Nacimiento:') !!}
                            <div class='input-group date' id='datetimepicker1'>
                                {!! Form::text('birthday', null, ['class' => 'form-control']) !!}
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="input-box">
                            {!! Form::label('genere', 'Género:') !!}
                            <div class="row">
                                <div class="col-xs-4">
                                    <label for="genere1" class="check">
                                        <input class="form-check-input" type="radio" name="genere" id="genere1" value="1">
                                        {!! Form::label('genere1', trans('app.common.man')) !!}
                                    </label>
                                </div>
                                <div class="col-xs-4">
                                    <label for="genere2" class="check">
                                        <input class="form-check-input" type="radio" name="genere" id="genere2" value="0">
                                        {!! Form::label('genere2', trans('app.common.woman')) !!}
                                    </label>
                                </div>
                                <div class="col-xs-4">
                                    <label for="genere3" class="check">
                                        <input class="form-check-input" type="radio" name="genere" id="genere3" value="2">
                                        {!! Form::label('genere3', trans('app.common.others')) !!}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="input-two space-80">
                        <div class="input-box">
                            {!! Form::label('phone', 'Teléfono: *') !!}
                            {!! Form::text('phone', null, ['class' => 'form-control', 'required' => 'required']) !!}
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
