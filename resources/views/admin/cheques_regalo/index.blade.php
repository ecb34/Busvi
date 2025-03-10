@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h3>
            Mis Cheques Regalo
            <a class="btn btn-primary pull-right" href="{!! url('admin/cheques_regalo/create') !!}">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Comprar
            </a> 
            <a class="btn btn-info pull-right" data-toggle="modal" href="#acepta_cheque" style="margin-right: 20px">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Introducir Código
            </a>            
           
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
                              <th>@lang('app.cheques_regalo.importe')</th>
                              <th>@lang('app.cheques_regalo.to_use_in_company')</th>
                              <th>@lang('app.cheques_regalo.estado')</th>
                             {{--  <th width="10%">@lang('app.cheques_regalo.actions')</th>        --}}
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
<div class="modal fade" id="acepta_cheque" tabindex="-1" role="dialog" aria-labelledby="modalAceptaChequeLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalEditPassLabel">Introduzca el código del cheque para poderlo usar</h4>
            </div>
        {!! Form::open(['url' => 'admin/cheques_regalo/aceptarCheque', 'id' => 'aceptaCheque']) !!}            
            <div class="modal-body">
                <div class="box box-primary">            
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('uuid', 'Código:') !!}
                                    {!! Form::text('uuid','',['class' => 'form-control', 'required']) !!}                            
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group col-xs-12">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                    <a href="{!! url()->previous() !!}" class="btn btn-default pull-right">Cancelar</a>
                </div>
            </div>
        {{ Form::close() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
 @include('layouts.datatables_js')
 <script type="text/javascript">
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
    }); 


    var cheques_regalo_datatable_url = "{{ url('admin/cheques_regalo/datatable')}}"; 

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
            {data: 'importe', name: 'importe'},
            {data: 'company_id', name: 'company_id'},  
            {data: 'status', name: 'status'},              
        ],        
        language: {"url": "{{asset('vendor/datatables/Spanish.json')}}"},    
    }); 

    

 </script>

@endsection