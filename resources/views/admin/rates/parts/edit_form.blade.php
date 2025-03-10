
        <!-- Name Field -->
        <div class="form-group col-sm-12">
            {!! Form::label('name', 'Nombre:') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <!-- Meses Field -->
        <div class="form-group col-xs-12 col-sm-6">
            {!! Form::label('months', 'Meses:') !!}
            {!! Form::number('months', null, ['class' => 'form-control', 'required' => 'required', 'min' => '0']) !!}
        </div>

        <!-- Coste Field -->
        <div class="form-group col-xs-12 col-sm-6">
            {!! Form::label('amount', 'Coste:') !!}
            {!! Form::number('amount', null, ['class' => 'form-control', 'required' => 'required', 'step' => '.01', 'min' => '0']) !!}
        </div>

        <!-- Submit Field -->
        <div class="form-group col-xs-12">
            {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('rates.index') !!}" class="btn btn-default pull-right">Cancelar</a>
        </div>