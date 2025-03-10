@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Crear Evento
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">            
            {!! Form::open(['route' => 'eventos.store', 'id' => 'createEvento', 'enctype' => 'multipart/form-data']) !!}       
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-8">
                        <div class="form-group">
                            {!! Form::label('nombre', 'Nombre:') !!}
                            {!! Form::text('nombre','',['class' => 'form-control', 'required']) !!}                            
                        </div>
                    </div>
                     <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            {!! Form::label('categoria_evento_id', 'Categoría:') !!}
                            
                                {!! Form::select('categoria_evento_id',$categorias,null, ['class' => 'form-control , select2', 'placeholder' => 'Escoja Categoría', 'id' => 'categoria_evento_id']) !!}                            
                            
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('desde', 'Inicio del evento:') !!}
                            <div class='input-group date' id='desde_timepicker'>
                                {!! Form::text('desde', old('desde'), ['class' => 'form-control', 'required']) !!}     
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
                                {!! Form::text('hasta', old('hasta'), ['class' => 'form-control']) !!}     
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('aforo_maximo', 'Aforo Máximo:') !!}
                            {!! Form::number('aforo_maximo',$evento->aforo_maximo,['class' => 'form-control', 'step' => '1']) !!}    
                        </div>
                    </div>
                     <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            {!! Form::label('precio', 'Importe:') !!}
                            {!! Form::number('precio','',['class' => 'form-control', 'required', 'step' => '0.01']) !!}    
                            <small>Este importe se incrementará en un {{$comision}}% , por gastos de la plataforma</small>                        
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('company_id', 'Negocio:') !!}
                            @if($companies->count() > 0)
                                {!! Form::select('company_id',$companies,$selected_company, ['class' => 'form-control , select2', 'placeholder' => 'Limitar a un negocio', 'id' => 'company_id']) !!}                            
                            @else
                                {!! Form::text('no_company',null, ['class' => 'form-control', 'readonly']) !!}
                                <h5>Actualmente no hay negocios que acepten eventos, disculpe las molestias</h5>                            
                            @endif    
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-8">
                        <div class="form-group">
                            {!! Form::label('direccion', 'Dirección:') !!}
                            {!! Form::text('direccion','',['class' => 'form-control', 'required']) !!}                            
                            {!! Form::hidden('long','',['id' => 'long']) !!}                            
                            {!! Form::hidden('lat','',['id' => 'lat']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            {!! Form::label('poblacion', 'Población:') !!}
                            {!! Form::text('poblacion','',['class' => 'form-control', 'required']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('imagen', 'Imagen:') !!}  
                                                            <input type="file" name="imagen" value="" class="" accept="image/x-png,image/gif,image/jpeg">
                          
                          
                        </div>
                    </div>

                     <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('descripcion', 'Descripción:') !!}
                            {!! Form::textarea('descripcion','',['class' => 'form-control']) !!}                            
                        </div>
                    </div>
                   
                       <!-- Submit Field -->
                    <div class="form-group col-xs-12">
                         {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                        <a href="{!! url('eventos.index') !!}" class="btn btn-default pull-right">Cancelar</a>
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
   
</script>

@endsection