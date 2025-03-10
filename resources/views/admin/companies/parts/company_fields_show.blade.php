
<div class="col-xs-12">
    <!-- Name Field -->
    <div class="form-group">
        {!! Form::label('name_comercial', 'Nombre Comercial *:') !!}
        {{ $company->name_comercial }}
    </div>

    <!-- Name Field -->
    {{-- <div class="form-group">
        {!! Form::label('name', 'Nombre Fiscal:') !!}
        {{ $company->name }}
    </div> --}}

    <!-- CIF Field -->
    {{-- <div class="form-group">
        {!! Form::label('cif', 'CIF / NIF / NIE:') !!}
        {{ $company->cif }}
    </div> --}}

    <!-- Provincia Field -->
    <div class="form-group">
        {!! Form::label('province', 'Provincia: *') !!}
        {{ $company->province }}
    </div>

    <!-- CP Field -->
    <div class="form-group">
        {!! Form::label('cp', 'Cod. Postal:') !!}
        {{ $company->cp }}
    </div>

    <!-- City Field -->
    <div class="form-group">
        {!! Form::label('city', 'Ciudad:') !!}
        {{ $company->city }}
    </div>

    <!-- Address Field -->
    <div class="form-group">
        {!! Form::label('address', 'Dirección:') !!}
        {{ $company->address }}
    </div>

    <!-- Phone Field -->
    <div class="form-group">
        {!! Form::label('phone', 'Teléfono: *') !!}
        {{ $company->phone }}
    </div>

    <!-- Phone2 Field -->
    <div class="form-group">
        {!! Form::label('phone2', 'Teléfono 2:') !!}
        {{ $company->phone2 }}
    </div>

    <!-- Logo Field -->
    <div class="form-group">
        <div class="row">
            <div class="col-xs-4">
                <img src="{{ asset('/img/companies/' . $company->logo) }}" alt="Busvi" class="img-rounded w-100 item-img">
            </div>
        </div>
    </div>

    <!-- Schedule Field -->
    <div class="form-group">
        {!! Form::label('schedule', 'Horario') !!}<br>
        @include('public.parts.schedule_days')
    </div>

    <!-- Schedule Field -->
    <div class="form-group">
        {!! Form::label('sector_id', 'Sector:') !!}
        {{ $company->sector->name }}
    </div>
</div>