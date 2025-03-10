@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h3>
            Eventos en mi negocio
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
                              <th>@lang('app.eventos.fecha')</th>
                              <th>@lang('app.eventos.importe')</th>
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



@section('scripts')
 @include('layouts.datatables_js')
 <script type="text/javascript">
    var evento_importe = null;
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
      
    }); 

  
    var eventos_datatable_url = "{{ url('admin/eventos/enMiNegocioDatatable')}}"; 
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
                {data: 'desde', name: 'desde'},
                {data: 'precio', name: 'precio'},
                {data: 'n_asistentes', name: 'n_asistentes', searchable:false, orderable:false},  
                {data: 'actions', name: 'actions'},              
            ],        
            language: {"url": "{{asset('vendor/datatables/Spanish.json')}}"},    
        }); 


    function validar(id){
        $.post("{{url('admin/eventos/validar')}}",{'evento_id': id , 'value' : '1'}, function (){
            eventos_table.ajax.reload();
        });
    }

    function denegar(id){
        $.post("{{url('admin/eventos/validar')}}",{'evento_id': id , 'value' : '0'}, function (){
            eventos_table.ajax.reload();
        }); 
    }


 </script>

@endsection