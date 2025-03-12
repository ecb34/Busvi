@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Error 500 - Algo ha fallado
        </h1>
    </section>
    <div class="content" >
        @include('adminlte-templates::common.errors')
        <div class="error-page">
            <h2 class="headline text-yellow"> 500</h2>

            <div class="error-content">
              <h3><i class="fa fa-warning text-yellow"></i> Oops! Error en el servidor.</h3>

              <p>
                Probablemente algún becario ha hecho algo de las suyas...
                Mientras nos hacemos cargo, por favor ...  <a href="/">Vuelve atrás</a> o llama al servicio técnico.
              </p>
            </div>
        </div>
    </div>        
@endsection




