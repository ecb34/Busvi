
    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Datos Negocio</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @include('admin.companies.parts.company_fields')
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Datos Negocio</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @include('admin.companies.parts.company_types')
                </div>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Datos Administrador</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @include('admin.companies.parts.user_fields')
                </div>
            </div>
        </div>
    </div>
    
    {{-- <div class="col-xs-12 col-sm-6">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Datos Bancarios</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @include('admin.companies.parts.user_fields')
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Submit Field -->
    <div class="form-group col-xs-12">
        <div class="box box-solid">
            <div class="box-body">
                {!! Form::submit('Guardar', ['class' => 'btn btn-primary', 'disabled' => $disabled]) !!}
                <a href="{!! route('companies.index') !!}" class="btn btn-default pull-right">
                    Cancelar
                </a>
                @if ($disabled)
                    <p class="text-warning">
                        <small>
                            Es necesario que existan <a href="{{ route('sectors.index') }}">sectores</a> y <a href="{{ route('services.index') }}">servicios</a> para poder crear un negocio
                        </small>
                    </p>
                @endif
            </div>
        </div>
    </div>