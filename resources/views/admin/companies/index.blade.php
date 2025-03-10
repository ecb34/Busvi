@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Negocios
            @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
            <a class="btn btn-primary pull-right" href="{!! route('companies.create') !!}">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Crear
            </a>
            @endif
            @if (Auth::user()->role == 'superadmin')
                <a class="btn btn-primary pull-right" style="margin-right: 10px;" href="{!! route('companies.excel') !!}">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('app.common.export_excel') }}
                </a>
            @endif
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

    @include('admin.companies.scripts.index_scripts')
@endsection