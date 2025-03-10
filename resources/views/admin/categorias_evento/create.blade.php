@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Crear Categoria de Evento
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">            
            {!! Form::open(['route' => 'categorias_evento.store', 'id' => 'createCategoriaEvento']) !!}            
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('nombre', 'Nombre:') !!}
                            {!! Form::text('nombre','',['class' => 'form-control', 'required']) !!}                            
                        </div>
                    </div>
                    
                       <!-- Submit Field -->
                    <div class="form-group col-xs-12">
                        
                            {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                        
                        <a href="{!! url('categorias_evento.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                    </div>
                </div>        

            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection