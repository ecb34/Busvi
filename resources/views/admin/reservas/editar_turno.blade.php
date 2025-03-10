@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3>
            <?=trans('app.reservas.editar_turno')?>
            <button class="btn btn-danger pull-right eliminar_turno"><?=trans('app.common.eliminar')?></button>
        </h3>
    </section>

    <div class="content">

        <div class="box box-info">
            <form action="<?=\URL::action('Admin\ReservasController@postTurno', ['id' => $turno->id])?>" method="post" class="form-horizontal">
                <?=csrf_field()?>
                <div class="box-body">

                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label"><?=trans('app.reservas.nombre')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nombre" name="nombre" required value="<?=htmlentities($turno->nombre)?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="col-sm-2 control-label"><?=trans('app.reservas.descripcion')?></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?=htmlentities($turno->descripcion)?></textarea>
                        </div>
                    </div>

                    <div class="bootstrap-timepicker">
                        <div class="form-group">
                            <label for="hora_inicio" class="col-sm-2 control-label"><?=trans('app.reservas.hora_inicio')?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control timepicker" name="hora_inicio" id="hora_inicio" value="<?=date('H:i', strtotime($turno->inicio))?>" required>
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
                                    <input type="text" class="form-control timepicker" name="hora_fin" id="hora_fin" value="<?=date('H:i', strtotime($turno->fin))?>" required>
                                    <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label"><?=trans('app.reservas.plazas')?></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="plazas" name="plazas" min="1" step="1" value="<?=$turno->plazas?>"required>
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
                                <label><input type="checkbox" class="dia" name="lunes" value="1" <?=$turno->lunes ? 'checked="checked"' : ''?>> <?=trans('app.reservas.lunes')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="martes" value="1" <?=$turno->martes ? 'checked="checked"' : ''?>> <?=trans('app.reservas.martes')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="miercoles" value="1" <?=$turno->miercoles ? 'checked="checked"' : ''?>> <?=trans('app.reservas.miercoles')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="jueves" value="1" <?=$turno->jueves ? 'checked="checked"' : ''?>> <?=trans('app.reservas.jueves')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="viernes" value="1" <?=$turno->viernes ? 'checked="checked"' : ''?>> <?=trans('app.reservas.viernes')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="sabado" value="1" <?=$turno->sabado ? 'checked="checked"' : ''?>> <?=trans('app.reservas.sabado')?></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="dia" name="domingo" value="1" <?=$turno->domingo ? 'checked="checked"' : ''?>> <?=trans('app.reservas.domingo')?></label>
                            </div>
                        </div>
                    </div>

                    <div class="bootstrap-datepicker">
                        <div class="form-group">
                            <label for="fecha_inicio" class="col-sm-2 control-label"><?=trans('app.reservas.fecha_inicio')?></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input readonly type="text" class="form-control datepicker" name="fecha_inicio" id="fecha_inicio" value="<?=!is_null($turno->fecha_inicio) ? date('d/m/Y', strtotime($turno->fecha_inicio)) : ''?>" required>
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
                                    <input readonly type="text" class="form-control datepicker" name="fecha_fin" id="fecha_fin" value="<?=!is_null($turno->fecha_fin) ? date('d/m/Y', strtotime($turno->fecha_fin)) : ''?>" required>
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <div class="input-group-addon eliminar_fecha" fecha="fecha_fin"><i class="fa fa-times"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="box-footer text-right">
                    <button type="submit" class="btn btn-info"><?=trans('app.common.guardar_cambios')?></button>
                </div>
            </form>
        </div>

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?=trans('app.reservas.bloqueos_turno')?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-xs btn-primary" id="nuevo_bloqueo" style="margin-top: 4px;"><?=trans('app.reservas.nuevo_bloqueo')?></button>
                </div>
            </div>
            <div class="box-body">
                <div class="dt-responsive table-responsive">
	                <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
	                    <table id="listado" class="table table-striped table-hover" cellspacing="0">
                            <thead> 
                                <tr>
                                    <th><?=trans('app.reservas.fecha')?></th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
        
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

        $('.eliminar_turno').click(function(){

            swal(
                {
                    title: "<?=trans('app.common.estas_seguro')?>",
                    text: "<?=trans('app.common.esta_accion_no_se_puede_deshacer')?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "<?=trans('app.common.si')?>",
                    confirmButtonClass: "btn-danger",
                    cancelButtonText: "<?=trans('app.common.no')?>",
                }
            ).then((result) => {
                if (result.value) {

                    $.post('<?=\URL::action('Admin\ReservasController@postEliminarTurno')?>', { _token: '<?=csrf_token()?>', id: <?=$turno->id?> }, 
                        function(data){
                            document.location.href = '<?=\URL::action('Admin\ReservasController@getTurnos')?>';
                        })
                        .fail(function(data){
                            swal({ type: 'error', title: data.responseJSON.error });
                        });

                }
            });

        }); 

        $('#listado').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?=\URL::action('Admin\ReservasController@getBloqueosDatatable', ['id' => $turno->id])?>',
            },
            columns: [
                { data: 'fecha', className: '', orderable: true, searchable: true, render: render_fecha },
                { data: 'acciones', className: 'text-right', orderable: false, searchable: false, render: render_acciones },
            ],
            order: [[0, 'desc']],
            pageLength: 50,
            language: {
                emptyTable: '<?=trans('datatables.emptyTable')?>',
                info: '<?=trans('datatables.info')?>',
                infoEmpty: '<?=trans('datatables.infoEmpty')?>',
                infoFiltered: '<?=trans('datatables.infoFiltered')?>',
                lengthMenu: '<?=trans('datatables.lengthMenu')?>',
                loadingRecords: '<?=trans('datatables.loadingRecords')?>',
                processing: '<?=trans('datatables.processing')?>',
                search: '<?=trans('datatables.search')?>',
                zeroRecords: '<?=trans('datatables.zeroRecords')?>',
                paginate: {
                    first: '<?=trans('datatables.first')?>',
                    last: '<?=trans('datatables.last')?>',
                    next: '<?=trans('datatables.next')?>',
                    previous: '<?=trans('datatables.previous')?>'
                },
                aria: {
                    sortAscending: '<?=trans('datatables.sortAscending')?>',
                    sortDescending: '<?=trans('datatables.sortDescending')?>'
                }
            },
            paging: true,
            lengthChange: true,
            searching: false,
            ordering: true,
            info: true,
            autoWidth: true,
            stateSave: false,
            responsive: true,
        });

        $('#listado').on('draw.dt', function(){
            $('#listado .borrar').click(eliminar_bloqueo);
        });

        $('#nuevo_bloqueo').click(function(){
            $('#modalNuevoBloqueo').modal('show');
        });

        $('#datepicker').datepicker({
            weekStart: 1,
            maxViewMode: 0,
            language: "es",
            format: "dd/mm/yyyy",
        });

        $('#guardar_bloqueo').click(function(){

            var fecha = $('#datepicker').val();
            $.post('<?=\URL::action('Admin\ReservasController@postNuevoBloqueo', ['id' => $turno->id])?>', { _token: '<?=csrf_token()?>', fecha: fecha }, 
                function(){
                    $('#modalNuevoBloqueo').modal('hide');
                })
                .fail(function(data){
                    swal({ type: 'error', title: data.responseJSON.error });
                })
                .always(function(){
                    $('#listado').DataTable().ajax.reload();
                });

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

    function render_fecha(data, type, row){
        var m = moment(data);
        return m.format('DD/MM/YYYY');
    }

    function render_acciones(data, type, row){
        var botones = [];
        botones.push('<button class="btn btn-xs btn-danger borrar" data-id="' + row.id + '">borrar</button>');
        return botones.join(' ');
    }

    function eliminar_bloqueo(){
        var id = $(this).attr('data-id');
        swal(
            {
                title: "<?=trans('app.common.estas_seguro')?>",
                text: "<?=trans('app.common.esta_accion_no_se_puede_deshacer')?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "<?=trans('app.common.si')?>",
                confirmButtonClass: "btn-danger",
                cancelButtonText: "<?=trans('app.common.no')?>",
            }
        ).then((result) => {
            if (result.value) {

                $.post('<?=\URL::action('Admin\ReservasController@postEliminarBloqueo', ['id' => $turno->id])?>', { _token: '<?=csrf_token()?>', id: id })
                    .fail(function(data){
                        swal({ type: 'error', title: data.responseJSON.error });
                    })
                    .always(function(){
                        $('#listado').DataTable().ajax.reload();
                    });

            }
        });
    }

</script>
@endsection

@section('modals')
<!-- Modal -->
<div class="modal fade" id="modalNuevoBloqueo" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
                <form onsubmit="return false" class="form-horizontal">
                    
                    <div class="form-group">
                        <label for="datepicker" class="col-sm-2 control-label"><?=trans('app.reservas.fecha')?></label>
                        <div class="col-sm-10">
                            <div class="input-group date">
                                <input type="text" class="form-control pull-right" id="datepicker" name="fecha" readonly>
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>

                </form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=trans('app.common.cancel')?></button>
				<button type="button" class="btn btn-primary" id="guardar_bloqueo"><?=trans('app.common.save')?></button>
			</div>
		</div>
	</div>
</div>
@endsection