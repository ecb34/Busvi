@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h3>
            Cheques Regalo Pendientes de Pagos                       
            
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                     <table id="cheques_regalo-table" class="table table-responsive table-striped" width="100%">
                        <thead>
                          <tr>              
                              <th>@lang('app.cheques_regalo.fecha_alta')</th>
                              <th>@lang('app.cheques_regalo.destinatario')</th>
                              <th>@lang('app.cheques_regalo.email_destino')</th>
                              <th>@lang('app.cheques_regalo.importe')</th>
                              <th>@lang('app.cheques_regalo.to_use_in_company')</th>
                              <th>@lang('app.cheques_regalo.estado')</th>
                              <th width="10%">@lang('app.cheques_regalo.actions')</th>        
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
            {{ Form::open(['route' => ['chequeRegaloStripe'], 'method' => 'POST', 'id' => 'payment-form']) }}
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
                                    <input type="hidden" name="cheque_id" id="cheque_id">
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
 <script src="{{ asset('lib/simple-credit-card-validation-form/assets/js/jquery.payform.min.js') }}"></script>
 <script src="{{ asset('lib/simple-credit-card-validation-form/assets/js/script.js') }}"></script>
 <script type="text/javascript">
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
    }); 


    var cheques_regalo_datatable_url = "{{ url('admin/cheques_regalo/pendientesDatatable')}}"; 

    var cheques_regalo_table = $('#cheques_regalo-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
                url: cheques_regalo_datatable_url,
                
        },
        dom : 'i',
        bInfo : false,
        pageLength: -1,
        columns: [                            
            {data: 'created_at', name: 'created_at'},
            {data: 'nombre', name: 'nombre'},
            {data: 'email', name: 'email'},
            {data: 'importe', name: 'importe'},
            {data: 'company_id', name: 'company_id'},  
            {data: 'status', name: 'status'},  
            {data: 'actions', name: 'actions'},  
        ],        
        language: {"url": "{{asset('vendor/datatables/Spanish.json')}}"},    

    }); 

    function pagar_stripe(id, importe){
        $('#cheque_id').val(id);
        $('#amount').val(importe);
        $('#modalStripeDays').modal('show');
    }

    $('#modalStripeDays').on('hidden', function(){
        $('#cheque_id').val(' ');
        $('#amount').val(' ');
    });
 </script>

@endsection