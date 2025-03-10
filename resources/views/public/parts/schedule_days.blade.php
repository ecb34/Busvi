<div class="schedule_days_inputs">
    <div>
        {!! Form::label('schedule', 'Lunes') !!}
        <div class="{{ \Carbon\Carbon::today()->isMonday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->l)
                {{ $schedule->horario_ini1->l }} - {{ $schedule->horario_fin1->l }}
                @if ($schedule->horario_ini2->l)
                    , {{ $schedule->horario_ini2->l }} - {{ $schedule->horario_fin2->l }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
    <div>
        {!! Form::label('schedule', 'Martes') !!}
        <div class="{{ \Carbon\Carbon::today()->isTuesday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->m)
                {{ $schedule->horario_ini1->m }} - {{ $schedule->horario_fin1->m }}
                @if ($schedule->horario_ini2->m)
                    , {{ $schedule->horario_ini2->m }} - {{ $schedule->horario_fin2->m }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
    <div>
        {!! Form::label('schedule', 'Miércoles') !!}
        <div class="{{ \Carbon\Carbon::today()->isWednesday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->x)
                {{ $schedule->horario_ini1->x }} - {{ $schedule->horario_fin1->x }}
                @if ($schedule->horario_ini2->x)
                    , {{ $schedule->horario_ini2->x }} - {{ $schedule->horario_fin2->x }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
    <div>
        {!! Form::label('schedule', 'Jueves') !!}
        <div class="{{ \Carbon\Carbon::today()->isThursday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->j)
                {{ $schedule->horario_ini1->j }} - {{ $schedule->horario_fin1->j }}
                @if ($schedule->horario_ini2->j)
                    , {{ $schedule->horario_ini2->j }} - {{ $schedule->horario_fin2->j }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
    <div>
        {!! Form::label('schedule', 'Viernes') !!}
        <div class="{{ \Carbon\Carbon::today()->isFriday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->v)
                {{ $schedule->horario_ini1->v }} - {{ $schedule->horario_fin1->v }}
                @if ($schedule->horario_ini2->v)
                    , {{ $schedule->horario_ini2->v }} - {{ $schedule->horario_fin2->v }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
    <div>
        {!! Form::label('schedule', 'Sábado') !!}
        <div class="{{ \Carbon\Carbon::today()->isSaturday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->s)
                {{ $schedule->horario_ini1->s }} - {{ $schedule->horario_fin1->s }}
                @if ($schedule->horario_ini2->s)
                    , {{ $schedule->horario_ini2->s }} - {{ $schedule->horario_fin2->s }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
    <div>
        {!! Form::label('schedule', 'Domingo') !!}
        <div class="{{ \Carbon\Carbon::today()->isSunday() ? 'text-bold' : '' }}">
            @if ($schedule->horario_ini1->d)
                {{ $schedule->horario_ini1->d }} - {{ $schedule->horario_fin1->d }}
                @if ($schedule->horario_ini2->d)
                    , {{ $schedule->horario_ini2->d }} - {{ $schedule->horario_fin2->d }}
                @endif
            @else
                <p>{{ trans('app.common.closed') }}</p>
            @endif
        </div>
    </div>
</div>