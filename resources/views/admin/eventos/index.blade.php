@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h3>
            Eventos
            <a class="btn btn-primary pull-right" href="{!! url('admin/eventos/create') !!}">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Crear
            </a>                   
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                     <table id="eventos-table" class="table table-responsive table-striped" width="100%">
                        <thead>
                          <tr>              
                              <th>@lang('app.eventos.nombre')</th>
                              <th>@lang('app.eventos.organizador')</th>
                              <th>@lang('app.eventos.fecha')</th>
                              <th>@lang('app.eventos.fecha_hasta')</th>
                              <th>@lang('app.eventos.importe')</th>
                              <th>@lang('app.eventos.company')</th>
                              <th>@lang('app.eventos.direccion')</th>
                              <th>@lang('app.eventos.asistentes')</th>
                               <th width="10%">@lang('app.eventos.actions')</th>  
                            </tr>
                        </thead>    
                        <tbody>    
                        </tbody>
                     </table>
            </div>
        </div>
    </div>
@endsection

@section('modals')
<div class="modal fade" id="modalAsistire" tabindex="-1" role="dialog" aria-labelledby="modalAsistireLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalEditPassLabel">¿Cuántos vais a venir?</h4>
            </div>
            {!! Form::open(['url' => 'admin/eventos/apuntarse', 'id' => 'apuntarme_form', 'method' => 'POST']) !!}            
            <div class="modal-body">
                <div class="box box-primary">            
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    {!! Form::hidden('apuntame_evento_id', null, ['id' => 'apuntame_evento_id']) !!}
                                    {!! Form::label('plazas_reservadas', 'Plazas a solicitar:') !!}
                                    {!! Form::number('plazas_reservadas','',['class' => 'form-control', 'required', 'min' => 1, 'step' => 1]) !!}                            
                                    <span class="label label-danger" id="aforo"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group col-xs-12" id="botones_sin_precio">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary', 'onclick' => "return validar_apuntarse()"]) !!}
                     <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                </div>
                <div class="form-group col-xs-12" id="botones_con_precio">
                    <a href="#" onclick="pagar_paypal()"  class="btn btn-primary ">Paypal<i class="glyphicon glyphicon-eur"> </i> </a>
                     <a href="#" onclick="pagar_stripe()" class="btn btn-danger ">Tarjeta<i class="glyphicon glyphicon-eur"> </i> </a>
                     <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                    
                </div>
            </div>
        {{ Form::close() }}
        </div>
    </div>
</div>
    <!-- Modal -->
<div class="modal fade" id="modalStripeDays" tabindex="-1" role="dialog" aria-labelledby="modalStripeDaysLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['route' => ['eventoStripe'], 'method' => 'POST', 'id' => 'payment-form']) }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalEditPassLabel">Confirmar Pago</h4>
                </div>
                <div class="modal-body">
                    <div class="creditCardForm">
                        <div class="payment">
                            <div class="form-group">
                                <label for="cvv">Importe</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="amount" id="amount" value="" readonly>
                                    <input type="hidden" name="evento_id" id="evento_id">
                                    <input type="hidden" name="cliente_evento_id" id="cliente_evento_id">
                                    <span class="input-group-addon">€</span>
                                </div>
                            </div>
                            <div class="form-group" id="card-number-field">
                                <label for="cardNumber">Tarjeta</label>
                                <input type="text" class="form-control" id="cardNumber" name="card">
                            </div>
                            <div class="form-group" id="expiration-date">
                                <label>Fecha Caducidad</label>
                                <select name="month">
                                    <option value="01">Enero</option>
                                    <option value="02">Febrero </option>
                                    <option value="03">Marzo</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Mayo</option>
                                    <option value="06">Junio</option>
                                    <option value="07">Julio</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                                <select name="year">
                                    @for($i=intval(date('y'));$i< intval(date('y'))+10;$i++)
                                        <option value="{{$i}}"> {{'20'.$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group CVV">
                                <label for="cvv">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv">
                            </div>
                            <div class="form-group" id="credit_cards">
                                <img src="{{ asset('lib/simple-credit-card-validation-form/assets/images/visa.jpg') }}" id="visa">
                                <img src="{{ asset('lib/simple-credit-card-validation-form/assets/images/mastercard.jpg') }}" id="mastercard">
                                <img src="{{ asset('lib/simple-credit-card-validation-form/assets/images/amex.jpg') }}" id="amex">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary pull-left">Guardar</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<form action="" method="post" id="eliminar_evento">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
@endsection


@section('scripts')
 @include('layouts.datatables_js')
 <script type="text/javascript">
    var evento_importe = null;
    var maximas_plazas = 0;  
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
    }); 

    function validar_apuntarse(){
        if($('#plazas_reservadas').val() < 1){
            swal({
                type: "error",
                title: "Error en reserva",
                text: "No se puede reservar para 0 personas.",
                timer: 2500
            });
            return false;
        }
        return true;
    }

    @if($datatable == 'misEventos')
        var eventos_datatable_url = "{{ url('admin/eventos/misEventosDatatable')}}"; 
    @else
        var eventos_datatable_url = "{{ url('admin/eventos/datatable')}}"; 
    @endif

    var eventos_table = $('#eventos-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                    url: eventos_datatable_url,
            },
            dom : 'lfrtip',
            bInfo : false,
            pageLength: 50,
            order: [1,'asc'],
            columns: [                            
                {data: 'nombre', name: 'nombre'},
                {data: 'organizador_id', name: 'organizador_id'},
                {data: 'desde', name: 'desde'},
                {data: 'hasta', name: 'hasta'},
                {data: 'precio', name: 'precio'},
                {data: 'company_id', name: 'company_id'},  
                {data: 'direccion', name: 'direccion'},              
                {data: 'n_asistentes', name: 'n_asistentes', searchable:false, orderable:false},              
                {data: 'actions', name: 'actions'},              
            ],        
            language: {"url": "{{asset('vendor/datatables/Spanish.json')}}"},  
            initComplete: function(settings, json) {
                $('#eventos-table .borrar').click(function(){
                    
                    var id = $(this).attr('data-id');
                    var url = "{{route('eventos.destroy', ['#'])}}".replace('#', id);
                    
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

                            $('#eliminar_evento').attr('action', url).submit();

                        }
                    });

                });
            }
  
        }); 

     function pagar_stripe(){
        if($('#plazas_reservadas').val() > 0){
            $('#evento_id').val($('#apuntame_evento_id').val());
            $('#amount').val(evento_importe * parseInt($('#plazas_reservadas').val()));
            var form = $('#apuntarme_form');
            var url = form.attr('action');
            $.ajax({
               type: "POST",
               url: url,
               data: form.serialize(), // serializes the form's elements.
               success: function(data){
                   data = JSON.parse(data);
                   if(data.id){
                        $('#cliente_evento_id').val(data.id);
                        $('#modalStripeDays').modal('show');
                   } else {
                        swal({
                            type: "error",
                            title: "Error en reserva",
                            text: "No ha sido posible realizar la reserva en este momento",
                            timer: 2500
                        });
                   }
               },
               error: function(data){
                    swal({
                        type: "error",
                        title: "Error en reserva",
                        text: "No se puede reservar para 0 personas.",
                        timer: 2500
                    });
                    $('#modalStripeDays').modal('hide');
                    $('#modalAsistire').modal('hide');
               }

             });
        }else{
            swal({
                type: "error",
                title: "Error en reserva",
                text: "No se puede reservar para 0 personas.",
                timer: 2500
            });
        }    
    }

    function pagar_paypal(){
        if($('#plazas_reservadas').val() > 0){
            var form = $('#apuntarme_form');
            var url = form.attr('action');
            $.ajax({
               type: "POST",
               url: url,
               data: form.serialize(), // serializes the form's elements.
               success: function(data){
                  //  $('#modalAsistire').modal('hide');
                  id = JSON.parse(data).id;
                    url = "{{url('eventoPaypal')}}"+"/"+id; 
                    document.location.href = url;
                  
               },
               error: function(data){
                    swal({
                    type: "error",
                        title: "Error en reserva",
                        text: "No se puede reservar para 0 personas.",
                        timer: 2500
                    });
               }
             });    
        }else{
            swal({
                type: "error",
                title: "Error en reserva",
                text: "No se puede reservar para 0 personas.",
                timer: 2500
            });
        }
    }

    $('#modalStripeDays').on('hidden', function(){
        $('#evento_id').val(' ');
        $('#amount').val(' ');
    });

    function apuntarme(id, aforo, precio){
        $('#apuntame_evento_id').val(id);
        $('#plazas_reservadas').val(1);
        $('#aforo').html('Hay '+aforo+' plazas libres');
        $('#plazas_reservadas').attr('max',aforo); 
        maximas_plazas = parseInt(aforo);  
        evento_importe = parseFloat(precio);
        if(precio > 0){
            $('#botones_sin_precio').hide();
            $('#botones_con_precio').show(); 
        }else{
            $('#botones_sin_precio').show();
            $('#botones_con_precio').hide();
        }
        $('#modalAsistire').modal('show');           
    }

    $('#modalAsistire').on('hidden', function(){
        $('#evento_id').val('');
        $('#amount').val('');
        maximas_plazas = 0;
        $('#plazas_reservadas').val(1);
    });

    $('#plazas_reservadas').on('change',function(){
    if(parseInt($('#plazas_reservadas').val()) > maximas_plazas){
        $('#plazas_reservadas').val(maximas_plazas);
        swal({
            type: "error",
                title: "Error en reserva",
                text: "No se puede reservar más plazas de las libres.",
                timer: 2500
            });
    }
});

 </script>

@endsection