@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3><?=trans('app.reservas.listado_reservas')?></h3>
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
                                    <th><?=trans('app.reservas.turno')?></th>
                                    <th><?=trans('app.reservas.descripcion')?></th>
                                    <th><?=trans('app.reservas.plazas')?></th>
                                    <th><?=trans('app.reservas.cliente')?></th>
                                    <th><?=trans('app.reservas.telefono')?></th>
                                    <th><?=trans('app.reservas.email')?></th>
                                    <th><?=trans('app.reservas.estado')?></th>
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
                url: '<?=\URL::action('Admin\ReservasController@getReservasDatatables')?>',
                data: function(d){
                    d.estado = $('#listado_filter select[name="filtro_estado"]').val();
                }
            },
            columns: [
                { data: 'fecha', className: 'text-right', orderable: true, searchable: false, render: render_fecha },
                { data: 'turno.nombre', className: '', orderable: false, searchable: false },
                { data: 'turno.descripcion', className: '', orderable: false, searchable: false },
                { data: 'plazas', className: '', orderable: false, searchable: false },
                { data: 'cliente', className: '', orderable: false, searchable: true, render: render_cliente },
                { data: 'telefono', className: '', orderable: false, searchable: true, render: render_telefono },
                { data: 'email', className: '', orderable: false, searchable: true },
                { data: 'estado', className: '', orderable: false, searchable: true, render: render_estado },
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
            $('#listado .accion').click(accion_turno);
        });

        var html = '';

        html += '<label style="margin-right: 15px;">Estado &nbsp; <select name="filtro_estado" class="form-control" style="display: inline-block; width: auto">';
        html += '  <option value="todas"></option>';
        html += '  <option value="pendientes" selected="selected">Pendientes</option>';
        html += '  <option value="confirmadas">Confirmadas</option>';
        html += '  <option value="anuladas">Anuladas</option>';
        html += '</select></label>';

        $('#listado_filter').prepend(html);

        $('#listado_filter select[name="filtro_estado"]').change(function(){
            $('#listado').DataTable().ajax.reload();
        });

    });

    function render_fecha(data, type, row){
        var m = moment(data);
        return m.format('D/M/YY');
    }

    function render_cliente(data, type, row){
        if(row.user){
            return row.user.name + ' ' + row.user.surname;
        } else {
            return row.nombre;
        }
    }

    function render_telefono(data, type, row){
        if(row.user){
            return row.user.phone;
        } else {
            return row.telefono;
        }
    }

    function render_acciones(data, type, row){
        var botones = [];
        if(row.anulado == 0 && row.confirmado == 0){
            botones.push('<button class="btn btn-xs btn-danger accion" data-id="' + row.id + '" data-accion="anular">Anular</button>');
            botones.push('<button class="btn btn-xs btn-success accion" data-id="' + row.id + '" data-accion="confirmar">Confirmar</button>');
        } else {
            botones.push('<button class="btn btn-xs btn-warning accion" data-id="' + row.id + '" data-accion="pendiente">Volver a pendiente</button>');
        }
        return botones.join(' ');
    }

    function render_estado(data, type, row){
        var manual = row.user ? '' : ' (manual)';
        if(row.anulado == 1){
            return '<button class="btn btn-xs btn-danger">Anulada' + manual + '</button>';
        } else {
            if(row.confirmado == 1){
                return '<button class="btn btn-xs btn-success">Confirmada' + manual + '</button>';
            } else {
                return '<button class="btn btn-xs btn-default">Pendiente' + manual + '</button>';
            }
        }
    }

    function accion_turno(){
        
        var id = $(this).attr('data-id');
        var accion = $(this).attr('data-accion');

        $.post('<?=\URL::action('Admin\ReservasController@postEstadoReserva')?>', { _token: '<?=csrf_token()?>', id: id, accion: accion })
            .fail(function(data){
                swal({ type: 'error', title: data.responseJSON.error });
            })
            .always(function(){
                $('#listado').DataTable().ajax.reload();
            });

    }

</script>
@endsection