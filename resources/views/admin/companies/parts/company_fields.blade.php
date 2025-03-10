
<div class="col-xs-12">
    <!-- Name Field -->
    <div class="form-group">
        {!! Form::label('name_comercial', 'Nombre Comercial *:') !!}
        {!! Form::text('name_comercial', old('name_comercial'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Name Field -->
    <div class="form-group">
        {!! Form::label('name', 'Nombre Fiscal *:') !!}
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- CIF Field -->
    <div class="form-group">
        {!! Form::label('cif', 'CIF / NIF / NIE *:') !!}
        {!! Form::text('cif', old('cif'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Provincia Field -->
    <div class="form-group">
        {!! Form::label('province', 'Provincia: *') !!}
        {!! Form::select('province', $provinces, old('province'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- CP Field -->
    <div class="form-group">
        {!! Form::label('cp', 'Cod. Postal: *') !!}
        {!! Form::text('cp', old('cp'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- City Field -->
    <div class="form-group">
        {!! Form::label('city', 'Ciudad: *') !!}
        {!! Form::text('city', old('city'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Address Field -->
    <div class="form-group">
        {!! Form::label('address', 'Dirección *:') !!}
        {!! Form::text('address', old('address'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Phone Field -->
    <div class="form-group">
        {!! Form::label('phone', 'Teléfono: *') !!}
        {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Phone2 Field -->
    <div class="form-group">
        {!! Form::label('phone2', 'Teléfono 2:') !!}
        {!! Form::text('phone2', old('phone2'), ['class' => 'form-control']) !!}
    </div>

    <!-- Web Field -->
    <div class="form-group">
        {!! Form::label('web', 'Web:') !!}
        {!! Form::text('web', old('web'), ['class' => 'form-control', 'placeholder' => 'http://example.com']) !!}
    </div>

    <!-- Logo Field -->
    <div class="form-group">
        {!! Form::label('logo', 'Logotipo: (Tamaño aconsejable 400px x 400px)') !!}
        {!! Form::file('logo', ['accept' => 'image/x-png,image/gif,image/jpeg', 'required' => 'required']) !!}
        <p class="text-info">
            <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG</small>
        </p>
    </div>

    <!-- Sector Field -->
    <div class="form-group">
        {!! Form::label('sector_id', 'Sector *:') !!}
        {!! Form::select('sector_id', $sectors, old('name_comercial'), ['class' => 'form-control', 'placeholder' => 'Escoja sector...', 'required' => 'required']) !!}
    </div>

    <!-- Schedule Field -->
    <div class="form-group">
        {!! Form::label('schedule', 'Horario') !!}<br>
        <a href="#" class="btn btn-primary btn-modal-schedule">Editar Horarios</a>
    </div>
</div>