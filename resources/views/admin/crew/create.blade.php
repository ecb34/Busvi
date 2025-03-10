@extends('layouts.app')

@section('content')
    <section class="content-header">     
        <h3>Nuevo Profesional</h3>
    </section>
    <div class="content">
        {!! Form::open(['route' => ['crew.store'], 'method' => 'POST', 'files' => true]) !!}
            <div class="row">
                <div class="col-xs-12">
                    @include('errors.validations')
                </div>

                <div class="col-xs-6">
                    <div class="box box-primary">
                        <div class="box-body">
                                @include('admin.crew.parts.fields')
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="box box-info">
                        <div class="box-body">
                            @include('admin.crew.parts.fields_services_crew')
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- Submit Field -->
                            <div class="form-group">
                                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                                <a href="{!! route('users.index') !!}" class="btn btn-default pull-right">Cancelar</a>
                            </div>
                        </div>
                            <div class="col-xs-6">
                                <p class="text-danger">
                                    <small>
                                        * Es responsabilidad del negocio y de su administrador, cumplir con la ley de protección de datos cuando incorpora a sus profesionales en Busvi
                                    </small>
                                </p>
                            </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    <script>
        $('select[name="company_id"]').on('change', function () {
            var id = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                method: "GET",
                url: "{{ route('crew.ajaxGetServices') }}",
                data: {id: id},
                success: function (response) {
                    if (response)
                    {
                        $('.row-services-items').remove();
                        $('.services_content').append(response);
                    }
                    else
                    {
                        swal('Oops...', "Algo ha ido mal al intentar eliminar el bloqueo.", 'error');
                    }
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        });

        $('input[type="submit"]').on('click', function () {
            var form = $(this).parentsUntil('.content');
            
            $('body').prepend('<div class="overlay"></div>');

            $.each(form.find('input,select,textarea'), function () {
                if ($(this).prop('required') && ($(this).val() == '' || $(this).val() == null))
                {
                    console.log($(this))
                    $('.overlay').remove();
                }
            });
        });
    </script>
@endsection