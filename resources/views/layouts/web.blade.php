<!DOCTYPE html>
<html>
    <head>
        @include('public.parts.head')
    </head>
    <body>

        @include('public.parts.header')

        @yield('content')

        @include('public.parts.footer')
        @include('public.parts.modal_login')

        @yield('modals')

        @include('public.parts.index_scripts')
        @include('public.parts.counter_scripts')

        <script>
            @if (session('message'))
                $(document).ready(function(){
                    swal({
                        type: "{{ session('m_status') }}",
                        title: "{{ session('message') }}",
                    });
                });
            @endif
        </script>

        {{-- Este formulario es para hacer el logout en toda la web --}}
        {!! Form::open(['route' => ['logout'], 'style' => 'display: none;', 'method' => 'POST', 'id' => 'logout-form']) !!}
        {!! Form::close() !!}

    </body>
</html>
