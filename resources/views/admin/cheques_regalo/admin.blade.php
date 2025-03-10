@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h3>
            Control de cheques regalo
      
           
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                 {!! $dataTable->table(['class' => 'table table-hover dataTable table-striped table-responsive', 'id'=> 'cheques_regalo']) !!} 
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
{!! $dataTable->scripts() !!}
 <script type="text/javascript">
    var selected = [];
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
        $('#cheques_regalo tbody').on('click', 'tr', function () {
            $(this).find('input[type="checkbox"]').iCheck('toggle');
        });
    }); 
 </script>

@endsection