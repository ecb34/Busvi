@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3><?=trans('app.reservas.nuevo_turno')?></h3>
    </section>

    <div class="content">

        <div class="box box-info">
            <form action="<?=\URL::action('Admin\ReservasController@postNuevoTurno')?>" method="post" class="form-horizontal">
                <?=csrf_field()?>
                <div class="box-body">

                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label"><?=trans('app.reservas.nombre')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="col-sm-2 control-label"><?=trans('app.reservas.descripcion')?></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                        </div>
                    </div>

                    <div class="bootstrap-timepicker">
                        <div class="form-group">
                            <label for="hora_inicio" class="col-sm-2 control-label"><?=trans('app.reservas.hora_inicio')?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control timepicker" name="hora_inicio" id="hora_inicio" required>
                                    <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bootstrap-timepicker">
                        <div class="form-group">
                            <label for="hora_fin" class="col-sm-2 control-label"><?=trans('app.reservas.hora_fin')?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control timepicker" name="hora_fin" id="hora_fin" required>
                                    <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label"><?=trans('app.reservas.plazas')?></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="plazas" name="plazas" min="1" step="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?=trans('app.reservas.dias')?><br/>
                            <a class="seleccionar" que="todo"><?=trans('app.reservas.seleccionar_todo')?></a><br/>
                            <a class="seleccionar" que="nada"><?=trans('app.reservas.seleccionar_nada')?></a><br/>
                            <a class="seleccionar" que="invertir"><?=trans('app.reservas.seleccionar_invertir')?></a>
                        </label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="lunes" value="1"> <?=trans('app.reservas.lunes')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="martes" value="1"> <?=trans('app.reservas.martes')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="miercoles" value="1"> <?=trans('app.reservas.miercoles')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="jueves" value="1"> <?=trans('app.reservas.jueves')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="viernes" value="1"> <?=trans('app.reservas.viernes')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="sabado" value="1"> <?=trans('app.reservas.sabado')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="domingo" value="1"> <?=trans('app.reservas.domingo')?></label>
                            </div>
                        </div>
                    </div>

                    <div class="bootstrap-datepicker">
                        <div class="form-group">
                            <label for="fecha_inicio" class="col-sm-2 control-label"><?=trans('app.reservas.fecha_inicio')?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input readonly type="text" class="form-control datepicker" name="fecha_inicio" id="fecha_inicio" value="" required>
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <div class="input-group-addon eliminar_fecha" fecha="fecha_inicio"><i class="fa fa-times"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bootstrap-datepicker">
                        <div class="form-group">
                            <label for="fecha_fin" class="col-sm-2 control-label"><?=trans('app.reservas.fecha_fin')?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input readonly type="text" class="form-control datepicker" name="fecha_fin" id="fecha_fin" value="" required>
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <div class="input-group-addon eliminar_fecha" fecha="fecha_fin"><i class="fa fa-times"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="box-footer text-right">
                    <button type="submit" class="btn btn-info"><?=trans('app.reservas.crear_turno')?></button>
                </div>
            </form>
        </div>

    </div>
        
@endsection
    
@section('modals')
@endsection

@section('scripts')
<script>
    $(document).ready(function(){

        $('.timepicker').timepicker({
            showInputs: false,
            showMeridian: false,
            defaultTime: false,
            disableFocus: true
        });

        $('.datepicker').datepicker({
            weekStart: 1,
            maxViewMode: 0,
            language: "es",
            format: "dd/mm/yyyy",
        });

        $('.eliminar_fecha').click(function(){
            var fecha = $(this).attr('fecha');
            $('#' + fecha).val('');
        });

        $('a.seleccionar').click(function(){
            switch($(this).attr('que')){
                case 'todo':
                    $('input.dia').prop('checked', true);
                break;
                case 'nada':
                    $('input.dia').prop('checked', false);
                break;
                case 'invertir':
                    $('input.dia').each(function(){
                        $(this).prop('checked', !$(this).prop('checked'));
                    });
                break;
            }
        });

    });
</script>
@endsection