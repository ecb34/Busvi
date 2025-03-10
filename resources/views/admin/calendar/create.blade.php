@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>Nueva Cita</h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                @include('errors.validations')

                {!! Form::open(['route' => 'calendar.store', 'id' => 'createEvent']) !!}
                    {!! Form::hidden('date_day') !!}

                    <div class="row">
                        <div class="col-xs-12">
                            @if (Auth::user()->role != 'user')
                                <!-- Customer Field -->
                                <div class="form-group">
                                    {!! Form::label('search-customer', 'Usuario:') !!}
                                    {!! Form::text('search-customer', null, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('customer') !!}
                                </div>
                            @else
                                <!-- Customer Field -->
                                <div class="form-group">
                                    {!! Form::label('customer', 'Usuario:') !!}
                                    {!! Form::text('name-customer', Auth::user()->name, ['class' => 'form-control', 'readonly']) !!}
                                    {!! Form::hidden('customer', Auth::user()->id) !!}
                                </div>
                            @endif
                        </div>

                        <div class="col-xs-12 text-center">
                            <h4>Ahora busca por Negocio, Servicio o Producto</h4>
                        </div>

                        <div class="col-xs-12 {{ (! $company_selected) ? 'col-sm-6' : '' }}">
                            <!-- Name Field -->
                            <div class="form-group">
                                {!! Form::label('name', 'Negocio:') !!}

                                @if ($company_selected)
                                    {!! Form::text('search-company', $companies[$company_selected], ['class' => 'form-control', 'readonly']) !!}
                                    {!! Form::hidden('company', $company_selected) !!}
                                @else
                                    {!! Form::text('search-company', null, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('company') !!}
                                @endif
                                {{-- {!! Form::select('companies', $companies, $company_selected, ['class' => 'form-control companies', 'placeholder' => 'Escoja negocio...', 'required' => 'required', $company_selected ? 'disabled' : '']) !!} --}}
                            </div>
                        </div>

                        @if (! $company_selected)
                            <div class="col-xs-12 col-sm-6">
                                <!-- Name Field -->
                                <div class="form-group term-search-wrapper">
                                    {!! Form::label('term', 'Servicio/Producto:') !!}
                                    {!! Form::text('term', null, ['class' => 'form-control', 'placeholder' => 'Busca por el término que te interese']) !!}
                                    {{-- {!! Form::select('companies', $companies, $company_selected, ['class' => 'form-control companies', 'placeholder' => 'Escoja negocio...', 'required' => 'required', $company_selected ? 'disabled' : '']) !!} --}}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row containers"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('modals')
    @include('admin.calendar.parts.modal_event')
    @include('admin.calendar.parts.modal_special_search')
@endsection

@section('scripts')
    @include('admin.calendar.scripts.create_scripts')
@endsection
