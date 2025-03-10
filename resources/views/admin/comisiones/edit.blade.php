@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Editar Comision
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">            
            {!! Form::model($comision, ['route' =>  ['comisiones.update', $comision->id], 'method' => 'patch']) !!}
        
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="form-group">
                            {!! Form::label('nombre', 'Nombre:') !!}
                            {!! Form::text('nombre',$comision->nombre,['class' => 'form-control', 'required', 'readonly']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('porcentaje', 'Porcentaje:') !!}
                            {!! Form::number('porcentaje',$comision->porcentaje,['class' => 'form-control', 'required', 'step' => '0.01', 'max'=>'100']) !!}                            
                        </div>
                    </div>
                       <!-- Submit Field -->
                    <div class="form-group col-xs-12">
                        
                            {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                        
                        <a href="{!! url('comisiones.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                    </div>
                </div>        

            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection