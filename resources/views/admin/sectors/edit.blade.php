@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>
            {{ $sector->name }}
            <a href="{{ route('sectors.destroy', $sector) }}" class="btn btn-danger pull-right btn-remove">
                <i class="fa fa-trash" aria-hidden="true"></i> Eliminar Sector
            </a>
        </h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::model($sector, ['route' => ['sectors.update', $sector], 'method' => 'PUT', 'files' => true]) !!}

                        @include('admin.sectors.parts.edit_form')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::open(['route' => ['sectors.destroy', $sector], 'method' => 'DELETE', 'id' => 'deleteItem']) !!}
    {!! Form::close() !!}
@endsection

@section('scripts')
    @include('admin.sectors.scripts.edit_scripts')
@endsection