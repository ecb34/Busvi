
    <!-- Name Field -->
    <div class="form-group col-xs-12">
        {!! Form::label('name', 'Nombre:') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Minutos Field -->
    <div class="form-group col-xs-12 col-sm-4">
        {!! Form::label('min', 'Minutos:') !!}
        {!! Form::number('min', null, ['class' => 'form-control', 'required' => 'required', 'step' => '1', 'min'  => 0]) !!}
    </div>

    <!-- Coste Field -->
    <div class="form-group col-xs-12 col-sm-4">
        {!! Form::label('price', 'Precio:') !!}
        {!! Form::number('price', null, ['class' => 'form-control', 'step' => '0.01', 'min'  => 0]) !!}
    </div>

    @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
        <!-- Coste Field -->
        <div class="form-group col-xs-12 col-sm-4">
            {!! Form::label('company', 'Negocio:') !!}
            {!! Form::select('company', $companies, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    @endif

    <!-- Submit Field -->
    <div class="form-group col-xs-12">
        {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('services.index') !!}" class="btn btn-default pull-right">Cancelar</a>
    </div>