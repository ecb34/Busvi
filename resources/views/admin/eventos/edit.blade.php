@extends('layouts.app')

@section('content')

    @php
        $readonly = !($evento->es_editable || \Auth::user()->role == 'admin' || \Auth::user()->role == 'superadmin');
    @endphp

    <section class="content-header">
        <h3>
            Ficha de Evento {{$evento->nombre}}
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">            
            {!! Form::model($evento, ['route' =>  ['eventos.update', $evento->id], 'method' => 'patch', 'enctype' => 'multipart/form-data']) !!}
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-8">
                        <div class="form-group">
                            {!! Form::label('nombre', 'Nombre:') !!}
                            {!! Form::text('nombre',$evento->nombre,['class' => 'form-control', 'required', $readonly ? 'readonly' : '']) !!}                            
                        </div>
                    </div>
                     <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            {!! Form::label('categoria_evento_id', 'Categoría:') !!}
                            {!! Form::select('categoria_evento_id',$categorias,null, ['class' => 'form-control '.($readonly ? 'select2' : ''), 'placeholder' => 'Escoja Categoría', 'id' => 'categoria_evento_id', $readonly ? 'disabled' : '']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('desde', 'Inicio del evento:') !!}
                            <div class='input-group date' id='desde_timepicker'>
                                {!! Form::text('desde', !is_null($evento->desde) ? $evento->desde->format('d-m-Y H:i') : '', ['class' => 'form-control', 'required', $readonly ? 'readonly' : '']) !!}     
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('hasta', 'Fin del evento: (opcional)') !!}
                            <div class='input-group date' id='hasta_timepicker'>
                                {!! Form::text('hasta', !is_null($evento->hasta) ? $evento->hasta->format('d-m-Y H:i') : '', ['class' => 'form-control', $readonly ? 'readonly' : '']) !!}     
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>                           
                        </div>
                    </div>
                     <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('aforo_maximo', 'Aforo Máximo:') !!}
                            {!! Form::number('aforo_maximo',$evento->aforo_maximo,['class' => 'form-control', 'step' => '1', $readonly ? 'readonly' : '']) !!}    
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('precio', 'Importe:') !!}
                            {!! Form::number('precio',$evento->precio,['class' => 'form-control', 'required', 'step' => '0.01', $readonly ? 'readonly' : '']) !!}    
                            <small>Este importe se incrementará en un {{$comision}}% , por gastos de la plataforma</small>                        
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('company_id', 'Negocio:') !!}
                            @if($companies->count() > 0)
                                {!! Form::select('company_id',$companies,$selected_company, ['class' => 'form-control '.($readonly ? 'select2' : ''), 'placeholder' => 'Limitar a un negocio', 'id' => 'company_id', $readonly ? 'disabled' : '']) !!}                            
                            @else
                                {!! Form::text('no_company',null, ['class' => 'form-control', 'readonly']) !!}
                                <h5>Actualmente no hay negocios que acepten eventos, disculpe las molestias</h5>                            
                            @endif    
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-8">
                        <div class="form-group">
                            {!! Form::label('direccion', 'Dirección:') !!}
                            {!! Form::text('direccion',$evento->direccion,['class' => 'form-control', 'required', $readonly ? 'readonly' : '']) !!}                            
                            {!! Form::hidden('long',$evento->long,['id' => 'long']) !!}                            
                            {!! Form::hidden('lat',$evento->lat,['id' => 'lat']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            {!! Form::label('poblacion', 'Población:') !!}
                            {!! Form::text('poblacion',$evento->poblacion,['class' => 'form-control', 'required', $readonly ? 'readonly' : '']) !!}                            
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('imagen', 'Imagen:') !!}  
                            @if($evento->getFirstMediaUrl())
                                <div style="margin-bottom: 5px;">
                                    <img src="{{ asset($evento->getFirstMediaUrl()) }}" style="max-width: 100px; max-height: 100px; border: 1px solid #ccc; padding: 4px;">
                                    <button type="button" class="btn btn-danger btn-xs eliminar_imagen" {{ $readonly ? 'disabled' : '' }}>Eliminar imagen</button>
                                </div>
                            @endif
                            <input type="file" name="imagen" value="" class="" accept="image/x-png,image/gif,image/jpeg" {{ $readonly ? 'disabled' : '' }}>
                        </div>
                    </div>

                     <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('descripcion', 'Descripción:') !!}
                            {!! Form::textarea('descripcion',$evento->descripcion,['class' => 'form-control', $readonly ? 'readonly' : '']) !!}                            
                        </div>
                    </div>
                   
                       <!-- Submit Field -->
                    @if(!$readonly)
                    <div class="form-group col-xs-12">
                         {!! Form::submit('Guardar', ['class' => 'btn btn-primary', $readonly ? 'disabled' : '']) !!}
                        <a href="{!! url('eventos.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                    </div>
                    @endif
                </div>        

            </div>
        </div>

        <div class="clearfix"></div>

        <div class="box box-info">
            <div class="box-body">
                <div class="dt-responsive table-responsive">
	                <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
	                    <table id="listado" class="table table-striped table-hover" cellspacing="0">
                            <thead> 
                                <tr>
                                    <th><?=trans('app.common.name')?></th>
                                    <th><?=trans('app.common.surname')?></th>
                                    <th><?=trans('app.common.email')?></th>
                                    <th><?=trans('app.common.telefono')?></th>
                                    <th><?=trans('app.eventos.plazas_reservadas')?></th>
                                    <th><?=trans('app.eventos.pagado')?></th>
                                    <th><?=trans('app.eventos.asistencia_confirmada')?></th>
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

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw&libraries=places"></script>
<script type="text/javascript">
    var address = null;
    $(document).ready(function() {          
        
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });  
        
        $('#company_id').on("change", function(e) {         
            url = "{{route('companies.ajaxShow')}}";
            $.post(url, {'company_id': $(this).val()}, function (response){
                $('#direccion').val(response.address).trigger('change');
                $('#poblacion').val(response.city).trigger('change');
            });
        });   
        
        $('#desde_timepicker').datetimepicker({
            locale: 'es',            
            format: 'DD-MM-YYYY HH:mm',
        });

        $('#hasta_timepicker').datetimepicker({
            locale: 'es',            
            format: 'DD-MM-YYYY HH:mm',
        });

        @if($evento->getFirstMediaUrl())
        $('.eliminar_imagen').click(function(){
            $.post("{{ \URL::action('Admin\EventoController@eliminarImagen', ['id' => $evento->id]) }}").always(function(){
                document.location.href = document.location.href;
            })
        });
        @endif

    }); 

    $('#direccion').on('change', function (){
        if($('#poblacion').val() > ''){
            address = $(this).val()+','+$('#poblacion').val();
            geodecode(address);
        }
    });

    $('#poblacion').on('change', function (){
        if($('#direccion').val() > ''){
            address = $('#direccion').val()+','+$(this).val();
            geodecode(address);
        }    
    });

    function geodecode(address) {
        geocoder = new google.maps.Geocoder();

        geocoder.geocode({
                'address' : address
            },
            function( results, status ) {
                if( status == google.maps.GeocoderStatus.OK ){
                    $('#lat').val(results[0].geometry.location.lat());
                    $('#long').val(results[0].geometry.location.lng());
                }
                else{
                    console.log('Geocode was not successful for the following reason: ' + status);
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            }
        );
    }

    // listado

    $(document).ready(function(){
        
        $('#listado').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?=\URL::action('Admin\EventoController@asistentesDatatable', ['evento' => $evento->id])?>',
                data: function(d){
                }
            },
            columns: [
                { data: 'name', className: '', orderable: true, searchable: true },
                { data: 'surname', className: '', orderable: true, searchable: true },
                { data: 'email', className: '', orderable: true, searchable: true },
                { data: 'phone', className: '', orderable: true, searchable: true },
                { data: 'plazas_reservadas', className: '', orderable: true, searchable: false },
                { data: 'pagado', className: '', orderable: true, searchable: false },
                { data: 'confirmacion_asistencia', className: '', orderable: true, searchable: false },
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

    });
   
</script>

@endsection