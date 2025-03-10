
<div class="row">
    <div class="col-xs-12">
        @include('errors.validations')
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        
        <div class="box box-primary">
            <div class="box-body">
                @include('admin.crew.parts.fields_edit')
            </div>
        </div>

        <div class="box box-info">
            <div class="box-body">
                @include('admin.crew.parts.fields_services_crew_edit')
            </div>
        </div>

        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Submit Field -->
                        <div class="">
                            {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                            <a href="{!! route('crew.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    @if (Auth::user()->company->type == 1 && Auth::user()->company->enable_fichajes)
    <div class="col-xs-6">
        <div class="box box-warning">
            <div class="box-body">
                <div class="box-header with-border">
                    <h3 class="box-title">Control horario</h3>
                    <? if(\Auth::user()->role == 'admin'){ ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right informe_fichajes" style="margin-top: -5px">Generar informe</button>
                    <? } ?>
                </div>
                <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="fichajes" class="table table-striped table-hover" cellspacing="0">
                            <thead>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Duración</th>
                                <th>&nbsp;</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
</div>