@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Comprar Cheques Regalo
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">            
            {!! Form::open(['route' => 'cheques_regalo.store', 'id' => 'createCheque']) !!}            
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('nombre', 'Destinatario:') !!}
                            {!! Form::text('nombre','',['class' => 'form-control', 'required']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('email', 'Email del destinatio:') !!}
                            {!! Form::email('email','',['class' => 'form-control', 'required']) !!}                            
                        </div>
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group">
                            {!! Form::label('company_id', 'Negocio:') !!}
                            @if($companies->count() > 0)
                                {!! Form::select('company_id',$companies,$selected_company, ['class' => 'form-control , select2', 'placeholder' => 'Limitar a un negocio']) !!}                            
                            @else
                                {!! Form::text('no_company',null, ['class' => 'form-control', 'readonly']) !!}
                                <h5>Actualmente no hay negocios que acepten cheques regalo, disculpe las molestias</h5>                            
                            @endif    
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('importe', 'Importe:') !!}
                            {!! Form::number('importe','',['class' => 'form-control', 'required', 'step' => '0.01', 'value' => 1, 'min' => 1]) !!}                            
                        </div>
                    </div>
                       <!-- Submit Field -->
                    <div class="form-group col-xs-12">
                        @if($companies->count() > 0)
                            {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                        @else    
                            {!! Form::submit('Guardar', ['class' => 'btn btn-primary', 'disabled']) !!}
                        @endif    
                        <a href="{!! url('cheques_regalo.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                    </div>
                </div>        

            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection