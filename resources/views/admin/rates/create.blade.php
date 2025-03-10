@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Nuevo Tarifa</h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => ['rates.store'], 'method' => 'POST']) !!}

                        @include('admin.rates.parts.create_form')
                        
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection