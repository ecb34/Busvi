<div class="row">

    <div class="col-xs-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('name', 'Nombre:') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-xs-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('dni', 'DNI:') !!}
            {!! Form::text('dni', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <!-- Username Field -->
        <div class="form-group">
            {!! Form::label('username', 'Username:') !!}
            {!! Form::text('username', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-sm-6">
        <!-- Email Field -->
        <div class="form-group">
            {!! Form::label('email', 'Email:') !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <!-- Password Field -->
        <div class="form-group">
            {!! Form::label('password', 'Contraseña:') !!}
            {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <!-- Password Field -->
        <div class="form-group">
            {!! Form::label('confirm_password', 'Confirmar contraseña:') !!}
            {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <!-- Img Field -->
        <div class="form-group">
            {!! Form::label('img', 'Avatar:') !!}
            {!! Form::file('img', ['accept'=>"image/x-png,image/gif,image/jpeg"]) !!}
            <p class="text-info">
                <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG</small>
            </p>
            {{-- {!! Form::file('img', null, ['class' => 'form-control', 'accept' => 'image/x-png,image/gif,image/jpeg', 'required' => 'required']) !!} --}}
        </div>
    </div>
</div>