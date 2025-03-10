@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Error 404 - Servicio no encontrado
        </h1>
    </section>
    <div class="content" >
        {{-- @include('adminlte-templates::common.errors') --}}
        <div class="error-page">
            <h2 class="headline text-yellow"> 404</h2>

            <div class="error-content">
              <h3><i class="fa fa-warning text-yellow"></i> Oops! Servicio No encontrado.</h3>

              <p>
                Probablemente ya estamos solventando el error.
                Mientras tanto  <a href="/">Vuelve atras</a> o llama al servicio técnico.
              </p>
            </div>
        </div>
    </div>        
@endsection




