
<div class="col-xs-12">
    <!-- Name Field -->
    <div class="form-group col-sm-9">
        {!! Form::label('name', 'Nombre:') !!}
        {!! Form::text('user_name', $company->admin->name, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Username Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('username', 'Username:') !!}
        {!! Form::text('username', $company->admin->username, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Email Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('email', 'Email:') !!}
        {!! Form::email('email', $company->admin->email, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Password Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('password', 'Contraseña:') !!}
        <p>
            <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modalEditPass">
                Modificar contraseña
            </a>
        </p>
    </div>
</div>