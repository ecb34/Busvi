@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>
            {{ $service->name }}
            <a href="{{ route('services.destroy', $service) }}" class="btn btn-danger pull-right btn-remove">
                <i class="fa fa-trash" aria-hidden="true"></i> Eliminar Service
            </a>
        </h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::model($service, ['route' => ['services.update', $service], 'method' => 'PUT', 'files' => true]) !!}

                        @include('admin.services.parts.edit_form')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::open(['route' => ['services.destroy', $service], 'method' => 'DELETE', 'id' => 'deleteItem']) !!}
    {!! Form::close() !!}
@endsection

@section('scripts')
    @include('admin.services.scripts.edit_scripts')
@endsection