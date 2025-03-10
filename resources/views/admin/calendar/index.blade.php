@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Calendario

            @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
                <a class="btn btn-primary pull-right" href="{!! route('calendar.create') !!}">
                    <i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Crear
                </a>
            @endif
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')
        
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                        {!! $datatable !!}
                    </div>
                </div>
            </div>
        </div>

        @if ($datatable_companies)
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="box box-danger">
                        <div class="box box-header">
                            <h4><i class="fa fa-heart" aria-hidden="true"></i> Favorito</h4>
                        </div>
                        <div class="box-body">
                            {!! $datatable_companies['datatable'] !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    {!! $script !!}

    @if ($datatable_companies)
        {!! $datatable_companies['script'] !!}
    @endif

    @include('admin.calendar.scripts.index_scripts')
@endsection