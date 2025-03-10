@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>{{ trans('app.admin.users.new_user') }}</h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                @include('errors.validations')

                <div class="row">
                    {!! Form::open(['route' => 'users.store', 'files' => true]) !!}
                        @include('admin.users.parts.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        @if (session('message'))
            swal({
                type: "{{ session('m_status') }}",
                title: "{{ session('message') }}",
                timer: 1500
            });
        @endif

        $('input[type="submit"]').on('click', function () {
            $('body').prepend('<div class="overlay"></div>');

            $.each($('.box-primary').find('form').find('input,select,textarea'), function () {
                if ($(this).prop('required') && ($(this).val() == '' || $(this).val() == null))
                {
                    console.log($(this))
                    $('.overlay').remove();
                }
            });
        });
        
        $('#datetimepicker1').datetimepicker({
            locale: 'es',
            // format: 'DD-MM-YYYY HH:mm',
            format: 'DD-MM-YYYY',
            disabledDates: [
                    "04/13/2018 18:40"
                ]
        });
    </script>
@endsection