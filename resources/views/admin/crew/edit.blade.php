@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>
            {{ $user->name }}

            @if (Auth::user()->role != 'crew')
                <a href="#" class="btn btn-danger pull-right btn-remove">
                    <i class="fa fa-user-times" aria-hidden="true"></i> Eliminar Profesional
                </a>
            @endif
        </h3>
    </section>

    <div class="content">
        {!! Form::model($user, ['route' => ['crew.update', $user], 'method' => 'PUT', 'files' => true]) !!}
            @include('admin.crew.parts.form_edit')
        {!! Form::close() !!}

        @include('admin.crew.parts.calendar_edit')

        {!! Form::open(['route' => ['crew.destroy', $user], 'method' => 'DELETE', 'id' => 'deleteItem']) !!}
        {!! Form::close() !!}

        @include('admin.crew.parts.modal_edit_password')

        <? if(\Auth::user()->role == 'admin'){ ?>
        @include('admin.crew.parts.modal_edit_fichaje')
        @include('admin.crew.parts.modal_informe_fichaje')
        <? } ?>

    </div>
@endsection

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('scripts')
    {!! $calendar->script() !!}

    @include('admin.crew.scripts.edit_scripts')
    @include('layouts.datatables_js')

    <script>
        var fichajes = null;
        $(document).ready(function(){

            fichajes = $('#fichajes').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ \URL::action('Admin\CrewController@getFichajes', [$user->id]) }}",
                },
                pageLength: 20,
                columns: [             
                    {data: 'inicio', name: 'inicio', defaultContent : ' ', class: 'text-right', orderable: false },       
                    {data: 'fin', name: 'fin', defaultContent : ' ', class: 'text-right', orderable: false  },
                    {data: 'duracion', name: 'duracion', defaultContent: ' ', class: 'text-right', orderable: false  },
                    {data: 'action', name: 'action', orderable: false, class: 'text-right' },  
                ],
                order: [0, 'desc'],
                initComplete: function(){
                    $('#fichajes_wrapper #fichajes_length').parent().parent().hide();
                }
            });

            $('#fichajes').on('draw.dt', function(){

                $('#fichajes button.edit').click(function(){

                    var fichaje = JSON.parse($(this).attr('fichaje'));
                    $('#modalEditFichaje input[name="fichaje_id"]').val(fichaje.id);
                    
                    var inicio = moment(fichaje.inicio);
                    $('#modalEditFichaje input[name="fecha_inicio"]').val(inicio.format('YYYY-MM-DD'));
                    $('#modalEditFichaje input[name="hora_inicio"]').val(inicio.format('HH:mm'));

                    if(fichaje.fin != null){

                        $('#modalEditFichaje input[name="fichaje_cerrado"]').prop('checked', true).trigger('change');

                        var fin = moment(fichaje.fin);
                        $('#modalEditFichaje input[name="fecha_fin"]').val(fin.format('YYYY-MM-DD'));
                        $('#modalEditFichaje input[name="hora_fin"]').val(fin.format('HH:mm'));

                    } else {

                        $('#modalEditFichaje input[name="fichaje_cerrado"]').prop('checked', false).trigger('change');

                        $('#modalEditFichaje input[name="fecha_fin"]').val('');
                        $('#modalEditFichaje input[name="hora_fin"]').val('');

                    }

                    $('#modalEditFichaje').modal('show');

                });

                $('#fichajes button.eliminar').click(function(){
                    swal({
                        title: 'Eliminar fichaje',
                        text: "¿Esta seguro que desea realizar esta acción?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Si',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.value){
                            
                            $.post("{{ \URL::action('Admin\CrewController@postDeleteFichaje') }}", {
                                _token: '<?=csrf_token()?>',
                                crew_id: <?=$user->id?>,
                                fichaje_id: $(this).attr('fichaje'),
                            }, function(data){

                                swal('', 'Fichaje eliminado', 'success');
                                $('#modalEditFichaje').modal('hide');
                                fichajes.ajax.reload();

                            }).fail(function(response) {
                                swal('Oops...', response.responseJSON.message, 'error');
                            });

                        }
                    });
                });

            });

            $('#modalEditFichaje input[name="fichaje_cerrado"]').change(function(){
                var checked = $(this).prop('checked');
                if(checked){
                    $('#modalEditFichaje .fin').show();
                } else {
                    $('#modalEditFichaje .fin').hide();
                }
            });

            $('#modalEditFichaje .guardar').click(function(){

                var fichaje_id = $('#modalEditFichaje input[name="fichaje_id"]').val();

                var fecha_inicio = $('#modalEditFichaje input[name="fecha_inicio"]').val();
                var hora_inicio = $('#modalEditFichaje input[name="hora_inicio"]').val();

                var fichaje_cerrado = $('#modalEditFichaje input[name="fichaje_cerrado"]').prop('checked');
                var fecha_fin = fichaje_cerrado ? $('#modalEditFichaje input[name="fecha_fin"]').val() : '';
                var hora_fin = fichaje_cerrado ? $('#modalEditFichaje input[name="hora_fin"]').val() : '';

                if(fecha_inicio == '' || hora_inicio == ''){
                    swal('Oops...', "La fecha/hora de inicio es obligatoria", 'error');
                    return false;
                }

                if(fichaje_cerrado && (fecha_fin == '' || hora_fin == '')){
                    swal('Oops...', "La fecha/hora de fin es obligatoria", 'error');
                    return false;
                }

                var moment_inicio = moment(fecha_inicio + ' ' + hora_inicio);
                if(fichaje_cerrado){

                    var moment_fin = moment(fecha_fin + ' ' + hora_fin);
                    if(moment_fin < moment_inicio){
                        swal('Oops...', "La fecha/hora de fin no puede ser anterior al inicio", 'error');
                        return false;
                    }

                }
                
                $.post("{{ \URL::action('Admin\CrewController@postFichaje') }}", {
                    _token: '<?=csrf_token()?>',
                    crew_id: <?=$user->id?>,
                    fichaje_id: fichaje_id,
                    inicio: fecha_inicio + ' ' + hora_inicio,
                    cerrado: fichaje_cerrado ? 1 : 0,
                    fin: fecha_fin + ' ' + hora_fin,
                }, function(data){

                    swal('', 'Fichaje actualizado', 'success');
                    $('#modalEditFichaje').modal('hide');
                    fichajes.ajax.reload();

                }).fail(function(response) {
                    swal('Oops...', response.responseJSON.message, 'error');
                });

            });

        });

        $('button.informe_fichajes').click(function(){
            $('#modalInformeFichaje').modal('show');
        });
        
    </script>
@endsection