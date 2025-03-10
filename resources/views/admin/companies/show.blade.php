@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>
            {{ $company->name }} <span class="badge">Negocio</span>

            @if ($company->isFavourite()->first())
                <label class="label label-danger"><i class="fa fa-heart" aria-hidden="true"></i> Es Favorito</label>
            @endif
        </h3>
    </section>
    <div class="content">
        <div class="row">
            @include('admin.companies.parts.show_fields')
        </div>

        <div class="clearfix"></div>
    </div>
@endsection

@section('scripts')
    {{-- {!! $calendar->script() !!} --}}
    
    @include('admin.companies.scripts.edit_scripts')
@endsection