@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
        	Cita
            @if (isset($event->customer))
                <span class="badge">{{ $event->customer->name }}</span>
            @endif
        </h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                @include('errors.validations')

                <p>
                    <strong>{{ $event->title }}</strong>
                </p>
                
                @if (isset($event->user))
                    <p>
                        Profesional: <strong>{{ $event->user->name }}</strong>
                    </p>
                @else
                    <p>
                        Profesional: <strong class="text-warning">Ya no existe</strong>
                    </p>
                @endif

                @if (isset($event->customer))
                    <p>
                        Usuario: <strong>{{ $event->customer->name }} {{ $event->customer->surname }}</strong>
                    </p>

                    <p>
                        Tlf. Usuario: <strong>{{ $event->customer->phone }}</strong>
                    </p>

                    <p>
                        eMail Usuario: <strong>{{ $event->customer->email }}</strong>
                    </p>
                @else
                    <p>
                        Usuario: <strong class="text-warning">Ya no existe</strong>
                    </p>
                @endif

                @if (isset($event->service))
                    <p>
                        Servicio: <strong>{{ $event->service->name }}</strong>
                    </p>
                @else
                    <p>
                        Servicio: <strong class="text-warning">Ya no existe</strong>
                    </p>
                @endif

                <p>
                    Fecha y Hora de Inicio: <strong>{{ \Carbon\Carbon::parse($event->start_date)->format('d / m / Y - H:i') }}</strong>
                </p>

                <p>
                    Fecha y Hora de Fin: <strong>{{ \Carbon\Carbon::parse($event->end_date)->format('d / m / Y - H:i') }}</strong>
                </p>
            </div>
        </div>
    </div>
@endsection