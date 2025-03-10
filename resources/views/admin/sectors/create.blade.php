@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Nuevo Sector</h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => ['sectors.store'], 'method' => 'POST', 'files' => true]) !!}

                        @include('admin.sectors.parts.create_form')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection