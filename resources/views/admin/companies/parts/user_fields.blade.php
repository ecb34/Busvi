
<div class="col-xs-12">
    <!-- Name Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('name', 'Nombre:') !!}
        {!! Form::text('user_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Surname Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('surname', 'Apellidos:') !!}
        {!! Form::text('user_surname', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Username Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('username', 'Username:') !!}
        {!! Form::text('username', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Email Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('email', 'Email:') !!}
        {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Password Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('password', 'Contraseña:') !!}
        {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
        <p class="text-info">
        	<small>
        		5 carácteres como mínimo
        	</small>
        </p>
    </div>

    <!-- Password Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('confirm_password', 'Confirmar contraseña:') !!}
        {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
</div>