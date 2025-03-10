
            <!-- Name Field -->
            <div class="form-group col-xs-12">
                {!! Form::label('name', 'Nombre:') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
            </div>

            <!-- File Field -->
            <div class="form-group col-xs-12">
                {!! Form::label('file', 'Imagen:') !!}
                {!! Form::file('image', null, ['class' => 'form-control']) !!}
            </div>

            <!-- Parent Field -->
            <div class="form-group col-xs-12">
                {!! Form::label('parent', 'Padre:') !!}
                {!! Form::select('sector_paretn_id', $all_sectors, null, ['class' => 'form-control']) !!}
            </div>

            <!-- Submit Field -->
            <div class="form-group col-xs-12">
                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                <a href="{!! route('sectors.index') !!}" class="btn btn-default pull-right">Cancelar</a>
            </div>