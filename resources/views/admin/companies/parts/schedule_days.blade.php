<div class="schedule_days_inputs">
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Lunes') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[l]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->l : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[l]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->l : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[l]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->l : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[l]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->l : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Martes') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[m]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->m : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[m]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->m : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[m]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->m : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[m]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->m : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Miércoles') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[x]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->x : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[x]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->x : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[x]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->x : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[x]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->x : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Jueves') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[j]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->j : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[j]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->j : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[j]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->j : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[j]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->j : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Viernes') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[v]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->v : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[v]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->v : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[v]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->v : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[v]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->v : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Sábado') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[s]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->s : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[s]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->s : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[s]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->s : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[s]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->s : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row m-b-35">
        <div class="col-xs-12">
            <small>- {!! Form::label('schedule', 'Domingo') !!} -</small>
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini1[d]', (isset($schedule->horario_ini1)) ? $schedule->horario_ini1->d : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin1[d]', (isset($schedule->horario_fin1)) ? $schedule->horario_fin1->d : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Inicio</small></strong>
            {!! Form::text('horario_ini2[d]', (isset($schedule->horario_ini2)) ? $schedule->horario_ini2->d : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
        <div class="col-xs-3">
            <strong><small>Fin</small></strong>
            {!! Form::text('horario_fin2[d]', (isset($schedule->horario_fin2)) ? $schedule->horario_fin2->d : null, ['class' => 'form-control', 'form' => 'companiesForm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
</div>