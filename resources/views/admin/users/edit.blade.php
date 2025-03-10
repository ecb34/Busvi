@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            {{ $user->name }} <span class="badge">Usuario</span>
            
            @if (Auth::user()->role == 'operator' || Auth::user()->role == 'superadmin')
                <a href="#" class="btn btn-danger pull-right btn-remove">
                    <i class="fa fa-user-times" aria-hidden="true"></i> {{ trans('app.admin.users.remove_user') }}
                </a>
            @endif

            @if (Auth::user()->role == 'user')
                <a href="#" class="btn btn-danger pull-right user-unsuscribe">
                    <i class="fa fa-ban" aria-hidden="true"></i> Dar de baja
                </a>
            @endif
        </h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT', 'files' => true]) !!}
                        @include('admin.users.parts.fields_edit')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        @if ($user->role == 'user')
            @if ($datatable_ev)
                <div class="box box-info">
                    <div class="box box-header">
                        <h4>
                            <i class="fa fa-heart" aria-hidden="true"></i> {{ trans('app.admin.users.my_dates') }}
                        </h4>
                    </div>
                    <div class="box-body">
                        {!! $datatable_ev !!}
                    </div>
                </div>
            @endif

            @if ($datatable)
                <div class="box box-danger">
                    <div class="box box-header">
                        <h4><i class="fa fa-heart" aria-hidden="true"></i> {{ trans('app.common.favourites') }}</h4>
                    </div>
                    <div class="box-body">
                        {!! $datatable !!}
                    </div>
                </div>
            @endif
        @endif
    </div>

    {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'DELETE', 'id' => 'deleteItem']) !!}
    {!! Form::close() !!}

    {!! Form::open(['route' => ['users.destroy', Auth::user()->id], 'method' => 'DELETE', 'id' => 'removeUser']) !!}
    {!! Form::close() !!}

    @include('admin.users.parts.modal_edit_password')
@endsection

@section('scripts')
    {!! $script_ev !!}
    {!! $script !!}
    
    <script>
        $('#datetimepicker1').datetimepicker({
            locale: 'es',
            // format: 'DD-MM-YYYY HH:mm',
            format: 'DD-MM-YYYY',
            disabledDates: [
                    "04/13/2018 18:40"
                ]
        });

        $('.user-unsuscribe').on('click', function (e) {
            e.preventDefault();

            unsuscribeUser()
        })

        function unsuscribeUser()
        {
            swal({
                type: "warning",
                title: "¿Estás seguro?",
                text: "Esta acción no se puede deshacer y perderas todo.",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa fa-thumbs-up"></i> Hazlo!'
            }).then((result) => {
                if (result.value)
                {
                    $('#removeUser').submit();
                }
            });
        }
    </script>

    @include('admin.users.scripts.edit_scripts')
@endsection