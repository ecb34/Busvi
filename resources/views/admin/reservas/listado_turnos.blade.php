@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3>
            <?=trans('app.reservas.listado_turnos')?>
            <a href="<?=\URL::action('Admin\ReservasController@getNuevoTurno')?>" class="btn btn-primary pull-right btn-add"><?=trans('app.reservas.nuevo_turno')?></a>
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
                                    <th><?=trans('app.reservas.turno')?></th>
                                    <th><?=trans('app.reservas.hora_inicio')?></th>
                                    <th><?=trans('app.reservas.hora_fin')?></th>
                                    <th><?=trans('app.reservas.dias')?></th>
                                    <th><?=trans('app.reservas.plazas')?></th>
                                    <th><?=trans('app.reservas.fecha_inicio')?></th>
                                    <th><?=trans('app.reservas.fecha_fin')?></th>
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
    
@section('modals')
@endsection

@section('scripts')
<script>
    $(document).ready(function(){

        $('#listado').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?=\URL::action('Admin\ReservasController@getTurnosDatatables')?>',
            },
            columns: [
                { data: 'nombre', className: '', orderable: true, searchable: true },
                { data: 'inicio', className: 'text-right', orderable: false, searchable: false, render: render_hora },
                { data: 'fin', className: 'text-right', orderable: false, searchable: false, render: render_hora },
                { data: 'dias', className: '', orderable: false, searchable: false, render: render_dias },
                { data: 'plazas', className: 'text-right', orderable: true, searchable: false },
                { data: 'fecha_inicio', className: 'text-right', orderable: false, searchable: false, render: render_fecha },
                { data: 'fecha_fin', className: 'text-right', orderable: false, searchable: false, render: render_fecha },
                { data: 'acciones', className: 'text-right', orderable: false, searchable: false, render: render_acciones },
            ],
            order: [[0, 'asc']],
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
            searching: true,
            ordering: true,
            info: true,
            autoWidth: true,
            stateSave: true,
            responsive: true,
        });

        $('#listado').on('draw.dt', function(){
            $('#listado .borrar').click(eliminar_turno);
        });

    });

    function render_hora(data, type, row){
        var m = data.split(':');
        return m[0] + ':' + m[1];
    }

    function render_fecha(data, type, row){
        if(data){
            var m = moment(data);
            return m.format('D/M/YYYY')
        }
        return data;
    }

    function render_dias(data, type, row){
        var dias = [];
        if(row.lunes == 1) dias.push('L');
        if(row.martes == 1) dias.push('M');
        if(row.miercoles == 1) dias.push('X');
        if(row.jueves == 1) dias.push('J');
        if(row.viernes == 1) dias.push('V');
        if(row.sabado == 1) dias.push('S');
        if(row.domingo == 1) dias.push('D');
        return dias.join('-');
    }

    function render_acciones(data, type, row){
        var botones = [];
        botones.push('<a href="<?=\URL::to('admin/reservas/turnos')?>/' + row.id + '" class="btn btn-xs btn-primary">Info</a>');
        botones.push('<button class="btn btn-xs btn-danger borrar" data-id="' + row.id + '">borrar</button>');
        return botones.join(' ');
    }

    function eliminar_turno(){
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

                $.post('<?=\URL::action('Admin\ReservasController@postEliminarTurno')?>', { _token: '<?=csrf_token()?>', id: id })
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