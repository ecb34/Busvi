@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Error 403 - Acceso no autorizado
        </h1>
    </section>
    <div class="content" >
        {{-- @include('adminlte-templates::common.errors') --}}
        <div class="error-page">
            <h2 class="headline text-red"> 403</h2>

            <div class="error-content">
              <h3><i class="fa fa-warning text-red"></i>Usted no tiene permisos para entrar aquí.</h3>

              <p>Por favor  <a href="/">Vuelve atras.</a> </p>
            </div>
        </div>     

    </div>
@endsection




