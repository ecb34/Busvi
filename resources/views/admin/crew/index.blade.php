@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Profesionales

            <?php $route = route('crew.create'); ?>
            @if ($disabled)
                <?php $route = '#'; ?>
            @endif

            <a class="btn btn-primary pull-right crew-create" href="{{ route('crew.create') }}">
                <i class="fa fa-user-plus" aria-hidden="true"></i> Crear
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

    @if ($disabled)
        <script>
            $('.crew-create').on('click', function (e) {
                e.preventDefault();

                swal({
                    type: "warning",
                    title: "Necesitas crear servicios para crear profesionales"
                });
            })
        </script>
    @endif

    @include('admin.users.scripts.index_scripts')
@endsection