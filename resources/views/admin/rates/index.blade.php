@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Tarifas
            <a class="btn btn-primary pull-right" href="{!! route('rates.create') !!}">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Crear
            </a>
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                {!! $datatable !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {!! $script !!}

    @include('admin.rates.scripts.index_scripts')
@endsection