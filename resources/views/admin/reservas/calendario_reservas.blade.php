@extends('layouts.app')

@section('content')

    <section class="content-header">     
        <h3><?=trans('app.reservas.calendario_reservas')?></h3>
    </section>

    <div class="content">

        <div class="box box-info">
            <div class="box-body">
                <div id="calendario_reservas"></div>
            </div>
        </div>

    </div>
        
@endsection

@section('scripts')
<script>
    $(document).ready(function(){

        $('#calendario_reservas').fullCalendar({
				events: '<?=action('Admin\ReservasController@getEventosCalendario')?>',
				firstDay: 1,                
				defaultView: 'month',
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month basicWeek listWeek'
				},
				validRange: function(nowDate) {
					return {
						start: nowDate.startOf('day'),
						end: nowDate.clone().add(12, 'months')
					};
				},
				height: 'auto',
				eventClick: function(info) {
                    if(typeof info.turno_id != 'undefined'){

                        var turno_id = info.turno_id;
                        var plazas_disponibles = info.plazas_disponibles;
                        var fecha = info.start.format('YYYY-MM-DD');

                        if(plazas_disponibles > 0){

                            $('#modalNuevaReserva input[name="turno_id"]').val(turno_id);
                            $('#modalNuevaReserva input[name="plazas_disponibles"]').val(plazas_disponibles);
                            $('#modalNuevaReserva input[name="fecha"]').val(fecha);
                            $('#modalNuevaReserva input[name="plazas"]').attr('max', plazas_disponibles);
                            $('#modalNuevaReserva input[name="plazas"]').val(1);
                            $('#modalNuevaReserva input[name="nombre"]').val('');
                            $('#modalNuevaReserva input[name="telefono"]').val('');
                            $('#modalNuevaReserva').modal('show');

                        } else {

                            swal({
                                title: "<?=trans('app.error')?>",
                                text: "No hay plazas disponibles en ese turno",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "<?=trans('app.cerrar')?>",
                                closeOnConfirm: false
                            });
                                
                        }

                    }
				},
				eventRender: function(event, element, view){

                    if(view.type == 'month' && event.vista == 'detalle'){
                        $(element).hide();
                    }

                    if(view.type != 'month' && event.vista == 'resumen'){
                        $(element).hide();
                    }

                    var html = '<div>' + event.title + '</div>';
                    if(view.type != 'listWeek'){
                        $(element).find('.fc-title').html(html);
                    } else {
                        $(element).find('.fc-list-item-title').html(html);
                        $(element).find('.fc-list-item-time').html('&nbsp;');
                    }

                },
            });
            
        $('#guardar_reserva').click(guardar_reserva);

    });

    function guardar_reserva(){

        var turno_id = parseInt($('#modalNuevaReserva input[name="turno_id"]').val());
        var plazas_disponibles = parseInt($('#modalNuevaReserva input[name="plazas_disponibles"]').val());
        var plazas = parseInt($('#modalNuevaReserva input[name="plazas"]').val());
        var fecha = $('#modalNuevaReserva input[name="fecha"]').val();
        var nombre = $('#modalNuevaReserva input[name="nombre"]').val();
        var telefono = $('#modalNuevaReserva input[name="telefono"]').val();

        if(plazas <= 0 || plazas > plazas_disponibles){
            
            swal({
                title: "<?=trans('app.error')?>",
                text: "El número de plazas seleccionado no es válido o no hay suficientes plazas libres",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "<?=trans('app.cerrar')?>",
                closeOnConfirm: false
            });

        } else {

            $.post('<?=\URL::action('Admin\ReservasController@postReservaManual')?>', {
                _token: '<?=csrf_token()?>',
                turno_id: turno_id,
                plazas: plazas,
                fecha: fecha,
                nombre: nombre,
                telefono: telefono,
            }).done(function(data){
                
                swal({ type: 'success', title: data.message });
                $('#modalNuevaReserva').modal('hide');
                $('#calendario_reservas').fullCalendar('refetchEvents');

            }).fail(function(data){
                
                swal({ type: 'error', title: data.responseJSON.error });

            });

        }

    }

</script>
@endsection

@section('modals')
<!-- Modal -->
<div class="modal fade" id="modalNuevaReserva" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
                <form onsubmit="return false" class="form-horizontal">

                    <input type="hidden" name="turno_id" value="">
                    <input type="hidden" name="plazas_disponibles" value="">
                    <input type="hidden" name="fecha" value="">
                    
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label"><?=trans('app.reservas.nombre')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control pull-right" id="nombre" name="nombre">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="col-sm-2 control-label"><?=trans('app.reservas.telefono')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control pull-right" id="telefono" name="telefono">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="plazas" class="col-sm-2 control-label"><?=trans('app.reservas.plazas')?></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control pull-right" id="plazas" name="plazas" min="1" step="1" value="1">
                        </div>
                    </div>

                </form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=trans('app.common.cancel')?></button>
				<button type="button" class="btn btn-primary" id="guardar_reserva"><?=trans('app.common.save')?></button>
			</div>
		</div>
	</div>
</div>
@endsection