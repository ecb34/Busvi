@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Nuevo Negocio</h3>
    </section>
    <div class="content">
        <div class="row">
            {!! Form::open(['route' => ['companies.store'], 'id' => 'companiesForm', 'method' => 'POST', 'files' => true]) !!}

                @include('admin.companies.parts.create_form')
                
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('modals')
    @include('admin.companies.parts.modal_schedule_days')
@endsection

@section('scripts')
    <script type="text/javascript">
        @if (session('message'))
            swal({
                type: "{{ session('m_status') }}",
                title: "{{ session('message') }}",
                timer: 4000
            });
        @endif

        $('.btn-modal-schedule').on('click', function (e) {
            e.preventDefault();

            $('#modalScheduleDays').modal('show');
        });

        $('.btn-copy').on('click', function (e) {
            var first_row = $('.schedule_days_inputs').find('.row').first().find('.col-xs-3');
            var ini1 = first_row.find('input[name="horario_ini1[l]"]').val();
            var fin1 = first_row.find('input[name="horario_fin1[l]"]').val();
            var ini2 = first_row.find('input[name="horario_ini2[l]"]').val();
            var fin2 = first_row.find('input[name="horario_fin2[l]"]').val();

            $('.schedule_days_inputs').find('.row').each(function () {
                $(this).find('.col-xs-3').first().find('input').val(ini1);
                $(this).find('.col-xs-3').eq(1).find('input').val(fin1);
                $(this).find('.col-xs-3').eq(2).find('input').val(ini2);
                $(this).find('.col-xs-3').last().find('input').val(fin2);
            })
        });
        
        $('input[name^="horario_"').datetimepicker({
            format: 'HH:mm'
        });

        $('input[type="submit"]').on('click', function () {
            $('body').prepend('<div class="overlay"></div>');

            $.each($('#companiesForm').find('input,select,textarea'), function () {
                if ($(this).prop('required') && ($(this).val() == '' || $(this).val() == null))
                {
                    console.log($(this))
                    $('.overlay').remove();
                }
            });
        });
    </script>
@endsection