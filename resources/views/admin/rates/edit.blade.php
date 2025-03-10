@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>
            {{ $rate->name }}
            <a href="{{ route('services.destroy', $rate) }}" class="btn btn-danger pull-right btn-remove">
                <i class="fa fa-trash" aria-hidden="true"></i> Eliminar Tarifa
            </a>
        </h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::model($rate, ['route' => ['rates.update', $rate], 'method' => 'PUT', 'files' => true]) !!}

                        @include('admin.rates.parts.edit_form')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::open(['route' => ['rates.destroy', $rate], 'method' => 'DELETE', 'id' => 'deleteItem']) !!}
    {!! Form::close() !!}
@endsection

@section('scripts')
    @include('admin.rates.scripts.edit_scripts')
@endsection