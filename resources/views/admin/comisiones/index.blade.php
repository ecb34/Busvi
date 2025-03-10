@extends('layouts.app')
@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')
    <section class="content-header">
        <h3>
            Control de Comisiones
             <a class="btn btn-primary pull-right" href="{!! url('admin/comisiones/create') !!}" style="display: none;">
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
                     {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable table-striped table-responsive']) !!} 
            </div>
        </div>
    </div>
@endsection



@section('scripts')
 @include('layouts.datatables_js')
{!! $dataTable->scripts() !!}
 

@endsection