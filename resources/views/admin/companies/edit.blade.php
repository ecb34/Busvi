@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>
            {{ $company->name }} <span class="badge">Negocio</span>
            @if ($company->blocked)
                &nbsp;<span class="label label-danger">BLOQUEADO</span>
            @endif

            @if (Auth::user()->role == 'admin' && $company->type == 0)
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a href="{{ route('companies.payment_premium', $company->id) }}" class="btn btn-success btn-lg">
                    Conviérte en Premium
                </a>
            @endif

            @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
                @if (! $company->blocked)
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#" class="btn btn-black btn-blocked btn-lg" data-val="1">
                        Bloquear Negocio
                    </a>
                @else
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#" class="btn btn-success btn-blocked btn-lg" data-val="0">
                        Desbloquear Negocio
                    </a>
                @endif
            @endif

            @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
                <a href="#" class="btn btn-danger pull-right btn-remove">Eliminar</a>
            @else
                <a href="#" class="btn btn-danger pull-right btn-get-down">
                    Solicitar Baja
                </a>
            @endif
        </h3>
    </section>
    <div class="content">
        <div class="row">
            {!! Form::model($company, ['route' => ['companies.update', $company], 'id' => 'companiesForm', 'method' => 'PUT', 'files' => true]) !!}

                @include('admin.companies.parts.edit_form')
                
            {!! Form::close() !!}
        </div>

        <div class="clearfix"></div>
    </div>

    {!! Form::open(['route' => ['companies.destroy', $company], 'method' => 'DELETE', 'id' => 'deleteItem']) !!}
    {!! Form::close() !!}

    {!! Form::open(['route' => ['companies.removeImageGallery'], 'method' => 'POST', 'id' => 'removeImageGallery']) !!}
        {{ Form::hidden('image_name') }}
        {{ Form::hidden('company_id', $company->id) }}
    {!! Form::close() !!}

    @endsection
    
@section('modals')
    @include('admin.companies.parts.modal_edit_password')
    @include('admin.companies.parts.modal_import_tags')
    @include('admin.companies.parts.modal_schedule_days')
    @include('admin.companies.parts.gallery-modal')
@endsection

@section('scripts')
    {{-- {!! $calendar->script() !!} --}}
    
    @include('admin.companies.scripts.edit_scripts')
@endsection