@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3>
            <?=trans('app.reservas.bloqueos')?>
            <button type="button" class="btn pull-right btn-primary" id="nuevo_bloqueo"><?=trans('app.reservas.nuevo_bloqueo')?></button>
        </h3>
    </section>

    <div class="content">

        <div class="box box-info">
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

        $('#listado').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?=\URL::action('Admin\ReservasController@getBloqueosDatatable', ['id' => 0])?>',
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
            $.post('<?=\URL::action('Admin\ReservasController@postNuevoBloqueo', ['id' => 0])?>', { _token: '<?=csrf_token()?>', fecha: fecha }, 
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

                $.post('<?=\URL::action('Admin\ReservasController@postEliminarBloqueo', ['id' => 0])?>', { _token: '<?=csrf_token()?>', id: id })
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