@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3><?=trans('app.reservas.listado_reservas_'.$filtro)?></h3>
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
                                    <th><?=trans('app.reservas.negocio')?></th>
                                    <th><?=trans('app.reservas.turno')?></th>
                                    <th><?=trans('app.reservas.horario')?></th>
                                    <th><?=trans('app.reservas.plazas_reservadas')?></th>
                                    <th><?=trans('app.reservas.telefono')?></th>
                                    <? if($filtro == 'proximas'){ ?>
                                    <th><?=trans('app.reservas.estado')?></th>
                                    <th>&nbsp;</th>
                                    <? } ?>
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
                url: '<?=\URL::action('Admin\ReservasController@getReservasClienteDatatables')?>',
                data: function(d){
                    d.filtro = '<?=$filtro?>';
                }
            },
            columns: [
                { data: 'fecha', className: 'text-right', orderable: true, searchable: false, render: render_fecha },
                { data: 'turno.company.name_comercial', className: '', orderable: false, searchable: true },
                { data: 'turno.nombre', className: '', orderable: false, searchable: true },
                { data: 'horario', className: '', orderable: false, searchable: false, render: render_horario },
                { data: 'plazas', className: '', orderable: false, searchable: false },
                { data: 'turno.company.phone', className: '', orderable: false, searchable: true, render: render_telefono },
                <? if($filtro == 'proximas'){ ?>
                { data: 'estado', className: '', orderable: false, searchable: true, render: render_estado },
                { data: 'acciones', className: 'text-right', orderable: false, searchable: false, render: render_acciones },
                <? } ?>
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

        <? if($filtro == 'proximas'){ ?>
        $('#listado').on('draw.dt', function(){
            $('#listado .anular').click(anular_reserva);
        });
        <? } ?>

    });

    function render_fecha(data, type, row){
        var m = moment(data);
        return m.format('D/M/YY');
    }

    function render_horario(data, type, row){
        var horario = '';
        horario += row.turno.inicio.substr(0, 5);
        horario += ' ';
        horario += row.turno.fin.substr(0, 5);
        return horario;
    }

    function render_telefono(data, type, row){
        var t = [];
        if(row.turno.company.phone && row.turno.company.phone != ''){
            t.push(row.turno.company.phone);
        }
        if(row.turno.company.phone2 && row.turno.company.phone2 != ''){
            t.push(row.turno.company.phone2);
        }
        return t.join(' ');
    }

    <? if($filtro == 'proximas'){ ?>

    function render_acciones(data, type, row){
        var botones = [];
        if(row.anulado == 0){
            botones.push('<button class="btn btn-xs btn-danger anular" data-id="' + row.id + '">Anular</button>');
        } 
        return botones.join(' ');
    }

    function render_estado(data, type, row){
        if(row.anulado == 1){
            return '<button class="btn btn-xs btn-danger">Anulada</button>';
        } else {
            if(row.confirmado == 1){
                return '<button class="btn btn-xs btn-success">Confirmada</button>';
            } else {
                return '<button class="btn btn-xs btn-default">Pendiente</button>';
            }
        }
    }
    
    function anular_reserva(){
        
        var id = $(this).attr('data-id');
        swal({
                title: "<?=trans('app.common.estas_seguro')?>",
                text: "<?=trans('app.common.esta_accion_no_se_puede_deshacer')?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "<?=trans('app.common.si')?>",
                confirmButtonClass: "btn-danger",
                cancelButtonText: "<?=trans('app.common.no')?>",
        }).then((result) => {
            if (result.value) {

                $.post('<?=\URL::action('Admin\ReservasController@postAnularReserva')?>', { _token: '<?=csrf_token()?>', id: id })
                    .fail(function(data){
                        swal({ type: 'error', title: data.responseJSON.error });
                    })
                    .always(function(){
                        $('#listado').DataTable().ajax.reload();
                    });

            }
        });

    }

    <? } ?>

</script>
@endsection