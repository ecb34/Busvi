@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Control de Eventos
        </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                     {!! $dataTable->table(['width' => '100%','class' => 'table  table-hover dataTable table-striped table-responsive']) !!} 
            </div>
        </div>
    </div>
@endsection



@section('scripts')
 @include('layouts.datatables_js')
{!! $dataTable->scripts() !!}
 <script type="text/javascript">
    $(document).ready(function() {    
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });    
          $('[data-toggle="tooltip"]').tooltip();

    }); 
 </script>

@endsection