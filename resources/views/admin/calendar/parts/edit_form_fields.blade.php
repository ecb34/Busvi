
    {{-- @if ($event->customer) --}}
        <div class="row">
            <div class="col-xs-12">
                <!-- Customer Field -->
                <div class="form-group">
                    {!! Form::label('search-customer', 'Usuario:') !!}
                    @if ($event->customer)
                        <p class="text-info">
                            <span class="text-bold">{{ $event->customer->name }}</span>
                        </p>
                        <p>
                            <a href="mailto:{{ $event->customer->email }}" class="text-success">{{ $event->customer->email }}</a> -
                            <a href="tel:{{ $event->customer->phone }}" class="text-success">{{ $event->customer->phone }}</a>
                        </p>
                    @else
                        <p class="text-info">
                            <span class="text-bold text-warning">El usuario ya no existe</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row company-row">
            <div class="col-xs-12">
                @if ($event->user)
                    <!-- Name Field -->
                    <div class="form-group">
                        {!! Form::label('copmanies', 'Negocio:') !!}
                        {!! Form::select('copmanies', $companies, $event->user->company->id, ['class' => 'form-control companies', 'placeholder' => 'Escoja negocio...', 'required' => 'required', 'disabled' => 'disabled']) !!}
                        {{ Form::hidden('crew', $event->user->id) }}
                        {{ Form::hidden('service', $event->service_id) }}
                    </div>
                @else
                    <!-- Name Field -->
                    <p class="text-info">
                            <span class="text-bold text-warning">El profesional y/o el negocio ya no existe</span>
                    </p>
                @endif
            </div>
        </div>

        {!! $crew_view !!}

        <div class="edit_calendar_zone"></div>

        @if ($event->customer && $event->user)
            <div class="row save-row">
                <div class="col-xs-12">
                    <!-- Submit Field -->
                    <div class="form-group">
                        {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                        <a href="{!! route('calendar.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                    </div>
                </div>
            </div>
        @endif
    {{-- @else
        <div class="row">
            <div class="col-xs-12">
                <!-- Customer Field -->
                <div class="form-group">
                    {!! Form::label('search-customer', 'Usuario:') !!}
                    <p class="text-info">
                        <span class="text-bold">No existe</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="row company-row">
            <div class="col-xs-12">
                <!-- Name Field -->
                <div class="form-group">
                    {!! Form::label('copmanies', 'Negocio:') !!}
                    {!! Form::select('copmanies', $companies, $event->user->company->id, ['class' => 'form-control companies', 'placeholder' => 'Escoja negocio...', 'required' => 'required', 'disabled' => 'disabled']) !!}
                </div>
            </div>
        </div>
    @endif --}}