

<div class="row">
    <div class="col-xs-12">

        <div class="box box-info">
            <div class="box-header">
                <h4><i class="fa fa-book"></i> {{ trans('app.reservas.proximas_reservas') }}</h4>
            </div>
            <div class="box-body">
                <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="listado_reservas_home" class="table table-striped table-hover" cellspacing="0">
                            <thead> 
                                <tr>
                                    <th><?=trans('app.reservas.fecha')?></th>
                                    <th><?=trans('app.reservas.negocio')?></th>
                                    <th><?=trans('app.reservas.turno')?></th>
                                    <th><?=trans('app.reservas.horario')?></th>
                                    <th><?=trans('app.reservas.plazas_reservadas')?></th>
                                    <th><?=trans('app.reservas.telefono')?></th>
                                    <th><?=trans('app.reservas.estado')?></th>
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
</div>

@section('scripts')
<script>
    $(document).ready(function(){

        $('#listado_reservas_home').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?=\URL::action('Admin\ReservasController@getReservasClienteDatatables')?>',
                data: function(d){
                    d.filtro = 'proximas';
                }
            },
            columns: [
                { data: 'fecha', className: 'text-right', orderable: true, searchable: false, render: render_fecha },
                { data: 'turno.company.name_comercial', className: '', orderable: false, searchable: true },
                { data: 'turno.nombre', className: '', orderable: false, searchable: true },
                { data: 'horario', className: '', orderable: false, searchable: false, render: render_horario },
                { data: 'plazas', className: '', orderable: false, searchable: false },
                { data: 'turno.company.phone', className: '', orderable: false, searchable: true, render: render_telefono },
                { data: 'estado', className: '', orderable: false, searchable: true, render: render_estado },
            ],
            order: [[0, 'asc']],
            pageLength: 10,
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

</script>
@append