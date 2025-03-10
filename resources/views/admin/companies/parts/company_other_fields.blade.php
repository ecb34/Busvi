
    <?php $disabled = (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator') ? '' : 'disabled'; ?>

    <!-- Switch Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('enable_events', 'Sistema de citas:') !!}
        <div class="row">
            <div class="col-xs-12 switch">                
                <input type="checkbox" name="enable_events" {{ $checked_events }} {{ $switch_enabled }} data-toggle="toggle" data-on="Habilitado" data-off="Deshabilitado" data-onstyle="success" data-offstyle="danger" data-style="ios" <?=$disabled?>>
            </div>
        </div>
    </div>

    <!-- Switch Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('enable_control', 'Sistema de fichajes:') !!}
        <div class="row">
            <div class="col-xs-12 switch">                
                <input type="checkbox" name="enable_fichajes" {{ $checked_fichajes }} {{ $switch_enabled }} data-toggle="toggle" data-on="Habilitado" data-off="Deshabilitado" data-onstyle="success" data-offstyle="danger" data-style="ios" <?=$disabled?>>
            </div>
        </div>
    </div>

    <!-- Switch Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('enable_reservas', 'Sistema de reservas:') !!}
        <div class="row">
            <div class="col-xs-12 switch">                
                <input type="checkbox" name="enable_reservas" {{ $checked_reservas }} {{ $switch_enabled }} data-toggle="toggle" data-on="Habilitado" data-off="Deshabilitado" data-onstyle="success" data-offstyle="danger" data-style="ios" <?=$disabled?>>
            </div>
        </div>
    </div>

    <div class="form-group col-sm-12">
        {!! Form::label('accept_cheque_regalo', 'Acepta cheque Regalo:') !!}
        <div class="row">
            <div class="col-xs-12 switch">                
                <input type="checkbox" name="accept_cheque_regalo" {{ $checked_accept_cheque_regalo }} {{ $switch_enabled }} data-toggle="toggle" data-on="Habilitado" data-off="Deshabilitado" data-onstyle="success" data-offstyle="danger" data-style="ios" <?=$disabled?>>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-12">
        {!! Form::label('accept_eventos', 'Acepta Eventos:') !!}
        <div class="row">
            <div class="col-xs-12 switch">                
                <input type="checkbox" name="accept_eventos" {{ $checked_accept_eventos }} {{ $switch_enabled }} data-toggle="toggle" data-on="Habilitado" data-off="Deshabilitado" data-onstyle="success" data-offstyle="danger" data-style="ios" <?=$disabled?>>
            </div>
        </div>
    </div>

    <!-- Services Field -->
    <div class="form-group col-sm-12 m-t-15">
        {!! Form::label('service_id', 'Servicios:') !!}
        <div class="row">
                @foreach ($services as $service)
                    <div class="checkbox col-xs-12 col-sm-6" style="margin-top: 0 !important;">
                        <label>
                            {!! Form::checkbox('services[]', $service->id, $company->admin->services->contains('service_id', $service->id)) !!}
                            {{ $service->name }}
                        </label>
                    </div>
                @endforeach
        </div>
    </div>

    <!-- Tags Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('tags', 'Etiquetas:') !!}
        <a href="{{ asset('ejemplo.csv') }}" download="ejemplo.csv">CSV de ejemplo</a>
        <div class="row">
            <div class="col-xs-12">
                {!! Form::text('tags', $tags, ['class' => 'form-control', 'data-role' => 'tagsinput']) !!}
                <br>
                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#modalImportTags">
                    Importar Etiquetas
                </a>
            </div>
        </div>
    </div>