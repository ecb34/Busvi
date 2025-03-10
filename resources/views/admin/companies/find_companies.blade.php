@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>Buscador de Negocios</h3>
    </section>
    <div class="content">
        <div class="row">
            {!! Form::open(['route' => 'companies.tags', 'method' => 'POST']) !!}
                <div class="col-xs-12">
                    {!! Form::text('tags', null, ['class' => 'form-control companies', 'placeholder' => 'Añade términos de búsqueda']) !!}
                    <br>
                    <button type="submit" href="#" class="btn-primary">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection