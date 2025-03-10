@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h3>
            Eventos a los que asistiré
            
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
                              <th>@lang('app.eventos.plazas_reservadas')</th>
                              <th>@lang('app.eventos.importe')</th>
                              <th>@lang('app.eventos.company')</th>
                              <th>@lang('app.eventos.direccion')</th>
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
@endsection


@section('scripts')
 @include('layouts.datatables_js')
 <script type="text/javascript">
    var evento_importe = null;
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
      
    }); 
    
    var eventos_datatable_url = "{{ url('admin/eventos/asistireDatatable')}}"; 
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
            {data: 'nombre_evento', name: 'nombre_evento'},
            {data: 'organizador_id', name: 'organizador_id'},
            {data: 'desde_evento', name: 'desde_evento'},
            {data: 'plazas_reservadas', name: 'plazas_reservadas'},
            {data: 'precio', name: 'precio'},
            {data: 'company_name', name: 'company_name'},  
            {data: 'direccion_evento', name: 'direccion_evento'},              
            {data: 'actions', name: 'actions'},              
        ],        
        language: {"url": "{{asset('vendor/datatables/Spanish.json')}}"},    
    }); 
    
    function pagar_stripe(id, importe){
        $('#cliente_evento_id').val(id);
        $('#amount').val(importe);
        $('#modalStripeDays').modal('show');
    }

    $('#modalStripeDays').on('hidden', function(){
        $('#cliente_evento_id').val(' ');
        $('#amount').val(' ');
    });

 </script>

@endsection