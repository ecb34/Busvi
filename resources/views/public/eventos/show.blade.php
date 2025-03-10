@extends('layouts.web')

@section('content')
    
    <div class="sin-blog">
        <div class="content">
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <img src="{{$evento->imagen}}">
                </div>
                <div class="col-xs-12 col-sm-8" style="margin-bottom: 20px">
                    <h2>{{ucwords($evento->nombre)}} <span class="badge">Evento</span></h2>
                    <h3 style="color:#ff6d1e">PvP: {{$evento->precio_final > 0 ? $evento->precio_final.' €' : 'Gratis'}}</h3>
                    @if($evento->categoria)
                    <br>Categoría: {{ $evento->categoria->nombre }}
                    @endif
                    <div style="margin-top: 5px;">
                        @if(!is_null($evento->company))
                        Negocio: <a href="{{ route('home.company', $evento->company) }}">{{ $evento->company->name_comercial }}</a>
                        @endif
                        <br>Dirección: {{ $evento->direccion }}
                        <br>{{ $evento->poblacion  }} 
                    </div>
                    <hr>
                    <h4>Desde: {{$evento->desde->format('d-m-Y H:i')}} - Hasta: {{$evento->hasta ? $evento->hasta->format('d-m-Y H:i') : ''}}</h4>
                    <p>Asistentes: {{$evento->n_asistentes}}</p>
                    @if(trim($evento->descripcion) != '')
                    <hr>
                    <p>{{$evento->descripcion}}</p>
                    @endif
                    <div class="blog-meta fix">
                        <span>Organiza:</span>
                        <br/><span class="author"><a href="mailto:{{ $evento->organizador->email }}">{{ $evento->organizador->name.' '.$evento->organizador->surname }}</a></span>
                        <br/><span class="comment"><a href="mailto:{{ $evento->organizador->email }}">{{ $evento->organizador->email }}</a></span>
                        <br/><span class="phone"><a href="tel:{{ $evento->organizador->phone }}">{{ $evento->organizador->phone }}</a>
                    </div>
                    <hr>
                    @if(\Auth::user())
                        <a href="#" onclick="apuntarme({{$evento->id}} ,'{{$evento->plazas_libres}}', {{$evento->precio_final}})" class="btn btn-primary pull-right" style="margin-right: 40px">Apúntame! <i class="glyphicon glyphicon-thumbs-up"> </i> </a>
                    @else
                        <a href="{{url('login')}}" class="btn btn-primary pull-right" style="margin-right: 40px">Debes estar registrado para apuntarte</a>
                    @endif    
                </div>
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
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
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
@endsection


@section('scripts')
<script type="text/javascript">
var evento_importe = null;
 var maximas_plazas = 0;
@if (session('message'))
    swal({
        type: "{{ session('m_status') }}",
        title: "{{ session('message') }}",
        timer: 2500
    });
@endif
$(document).ready(function() {  
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    });
}); 

function pagar_stripe(){
        $('#evento_id').val($('#apuntame_evento_id').val());
        $('#amount').val(evento_importe * parseInt($('#plazas_reservadas').val()));
        var form = $('#apuntarme_form');
        var url = form.attr('action');
        $.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(data){
              //  $('#modalAsistire').modal('hide');
                $('#modalStripeDays').modal('show');
           },
           error: function(data){
                console.log(data);
           }

         });
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
                    console.log(data);
               }
             });    
        }
    }

    $('#modalStripeDays').on('hidden', function(){
        $('#evento_id').val(' ');
        $('#amount').val(' ');
    });

    function apuntarme(id, aforo, precio){
        $('#apuntame_evento_id').val(id);
        $('#plazas_reservadas').val(0);
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
        $('#plazas_reservadas').val(0);
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
