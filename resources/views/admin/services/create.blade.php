@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Nuevo Servicio</h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => ['services.store'], 'method' => 'POST']) !!}

                        @include('admin.services.parts.create_form')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection