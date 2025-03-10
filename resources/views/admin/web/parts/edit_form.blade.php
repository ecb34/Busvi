
    <!-- Name Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('name', 'Nombre:') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- File Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            @if ($sector->img)
                <div class="col-xs-4">
                    <img src="/img/sectors/{{ $sector->img }}" alt="Busvi" class="img-rounded w-100 item-img">
                </div>
            @endif
            <div class="col-xs-6 {{ ($sector->img) ? 'hidden' : '' }} add-img">
                <div class="row">
                    @if ($sector->img)
                        <div class="col-xs-3">
                            <a href="#" class="btn btn-default btn-cancel-img">Cancelar</a>
                        </div>
                    @endif
                    <div class="col-xs-6">
                        {!! Form::label('file', 'Imagen:') !!}
                        {!! Form::file('image', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('parent', 'Padre:') !!}
        {!! Form::select('sector_parent_id', $all_sectors, $sector->sector_parent_id, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Order Field -->
    <div class="form-group col-xs-12">
        {!! Form::label('order', 'Orden:') !!}
        {!! Form::number('order', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Submit Field -->
    <div class="form-group col-xs-12">
        {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('sectors.index') !!}" class="btn btn-default pull-right">Cancelar</a>
    </div>