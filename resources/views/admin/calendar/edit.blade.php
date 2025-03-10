@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            Editar Cita
            @if (isset($event->customer))
                <span class="badge">{{ $event->customer->name }}</span>
            @endif
        </h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                @include('errors.validations')
                
                @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
                    {!! Form::open(['route' => ['calendar.destroy', $event->id], 'method' => 'DELETE']) !!}
                        <div class="row m-b-15">
                            <div class="col-xs-12 text-right">
                                {!! Form::submit('Eliminar Cita', ['class' => 'btn btn-danger btn-lg']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                @endif
                
                {!! Form::model($event, ['route' => ['calendar.update', $event->id], 'id' => 'editEvent', 'method' => 'PUT']) !!}
                    @include('admin.calendar.parts.edit_form_fields')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('modals')
    @if ($event->customer && $event->user)
        @include('admin.calendar.parts.modal_event_edit')
    @endif
@endsection

@section('scripts')
    @include('admin.calendar.scripts.edit_scripts')
@endsection