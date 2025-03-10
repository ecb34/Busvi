<div class="row">
    <div class="col-sm-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('name', 'Nombre:') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-sm-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('dni', 'DNI:') !!}
            {!! Form::text('dni', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
</div>

<div class="row">
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
        <!-- Email Field -->
        <div class="form-group">
            {!! Form::label('phone', 'Teléfono:') !!}
            {!! Form::text('phone', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    @if (Auth::user()->role != 'crew')
    <div class="col-sm-6">
        <!-- Email Field -->
        <div class="form-group">
        {!! Form::label('visible', '&nbsp;') !!}
        <div class="checkbox" style="margin-top: 5px">
            <label>
                {!! Form::checkbox('visible', 1, $user->visible) !!}
                Profesional visible
            </label>
        </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-sm-6">
        <!-- Edit Password -->
        <div class="form-group">
            {!! Form::label('roles', 'Contraseña:') !!}
            <p>
                <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modalEditPass">
                    Modificar contraseña
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Logo Field -->
<div class="form-group">
    <div class="row">
        @if ($user->img)
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        {!! Form::label('img', 'Avatar:') !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <img src="{{ url('/') . '/img/crew/' . $user->img }}" alt="Busvi" class="img-rounded w-100 item-img">
            </div>
        @endif
        <div class="col-xs-6 {{ ($user->img) ? 'hidden' : '' }} add-img">
            <div class="row">
                @if ($user->img)
                    <div class="col-xs-5">
                        <a href="#" class="btn btn-default btn-cancel-img">Cancelar</a>
                    </div>
                @endif
                <div class="col-xs-6">
                    {!! Form::file('img', ['accept' => 'image/x-png,image/gif,image/jpeg']) !!}
                </div>
            </div>
            <p class="text-info">
                <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG</small>
            </p>
        </div>
    </div>
</div>
