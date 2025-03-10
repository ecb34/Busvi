@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Editar Categoria de Evento
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">            
            {!! Form::model($categoria_evento, ['route' =>  ['categorias_evento.update', $categoria_evento->id], 'method' => 'patch']) !!}
         
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('nombre', 'Nombre:') !!}
                            {!! Form::text('nombre',$categoria_evento->nombre,['class' => 'form-control', 'required']) !!}                            
                        </div>
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