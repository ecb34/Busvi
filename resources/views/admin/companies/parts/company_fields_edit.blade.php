
<div class="col-xs-12">
    <!-- Name Field -->
    <div class="form-group">
        {!! Form::label('name_comercial', 'Nombre Comercial *:') !!}
        {!! Form::text('name_comercial', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Name Field -->
    <div class="form-group">
        {!! Form::label('name', 'Nombre Fiscal:') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- CIF Field -->
    <div class="form-group">
        {!! Form::label('cif', 'CIF / NIF / NIE:') !!}
        {!! Form::text('cif', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Provincia Field -->
    <div class="form-group">
        {!! Form::label('province', 'Provincia: *') !!}
        {!! Form::text('province', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- CP Field -->
    <div class="form-group">
        {!! Form::label('cp', 'Cod. Postal:') !!}
        {!! Form::text('cp', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- City Field -->
    <div class="form-group">
        {!! Form::label('city', 'Ciudad:') !!}
        {!! Form::text('city', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Address Field -->
    <div class="form-group">
        {!! Form::label('address', 'Dirección:') !!}
        {!! Form::text('address', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Phone Field -->
    <div class="form-group">
        {!! Form::label('phone', 'Teléfono: *') !!}
        {!! Form::text('phone', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Phone2 Field -->
    <div class="form-group">
        {!! Form::label('phone2', 'Teléfono 2:') !!}
        {!! Form::text('phone2', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Web Field -->
    <div class="form-group">
        {!! Form::label('web', 'Web:') !!}
        {!! Form::text('web', null, ['class' => 'form-control', 'placeholder' => 'http://example.com']) !!}
    </div>

    <!-- Logo Field -->
    <div class="form-group">
        <div class="row">
            @if ($company->logo)
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12">
                            {!! Form::label('logo', 'Logotipo: (Tamaño aconsejable 400px x 400px)') !!}
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <img src="{{ asset('/img/companies/' . $company->logo) }}" alt="Busvi" class="img-rounded w-100 item-img">
                </div>
            @endif
            <div class="col-xs-6 {{ ($company->logo) ? 'hidden' : '' }} add-img">
                <div class="row">
                    @if ($company->logo)
                        <div class="col-xs-5">
                            <a href="#" class="btn btn-default btn-cancel-img">Cancelar</a>
                        </div>
                    @endif
                    <div class="col-xs-6">
                        {!! Form::file('logo', ['accept'=>"image/x-png,image/gif,image/jpeg"]) !!}
                    </div>
                </div>
                <p class="text-info">
                    <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Schedule Field -->
    <div class="form-group">
        {!! Form::label('schedule', 'Horario') !!}<br>
        <a href="#" class="btn btn-primary btn-modal-schedule">Modificar Horarios</a>
    </div>

    <!-- Schedule Field -->
    <div class="form-group">
        {!! Form::label('sector_id', 'Sector:') !!}
        {!! Form::select('sector_id', $sectors, null, ['class' => 'form-control', 'placeholder' => 'Escoja sector...', 'required' => 'required']) !!}
    </div>
</div>