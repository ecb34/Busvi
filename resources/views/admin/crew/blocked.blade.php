@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            {{ $user->name }} <span class="badge">Bloqueo/Desbloqueo días y horas</span>
        </h3>
    </section>

    <div class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalAddBlockToEvent">
                            <i class="fa fa-calendar-times-o" aria-hidden="true"></i> Añadir bloqueo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body">
                        {!! $datatable !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {!! $script !!}

    @include('admin.crew.scripts.blocked_scripts')
@endsection

@section('modals')
    @include('admin.crew.parts.modal_add_block_to_event')
@endsection