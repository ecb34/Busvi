<script type="text/javascript">
    @if (session('message'))
        swal({
            type: "{{ session('m_status') }}",
            title: "{{ session('message') }}",
            timer: 1500
        });
    @endif

                $('input').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass: 'iradio_flat-blue'
                });
        
    $('input[name="all_day"]').on('ifChecked', function(event){
        $('input[name="end_date"]').prop('disabled', true);
        $('input[name="end_date"]').val('');
    });
    $('input[name="all_day"]').on('ifUnchecked', function(event){
        $('input[name="end_date"]').prop('disabled', false);
    });

    $('.calendar').datetimepicker({
        locale: 'es',
        format: 'DD-MM-YYYY HH:mm'
    });

    $('.calendar-time').datetimepicker({
        locale: 'es',
        format: 'LT'
    });
    
    $('#datatable_events').find('tr').on('mouseenter', function () {
        $(this).find('td').last().append('<a href="#" class="btn-rm text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>');

        setActionRemove($('.btn-rm'));
    }).on('mouseleave', function () {
        $(this).find('td').last().find('.btn-rm').remove();
    });

    function setActionRemove(elem)
    {
        elem.on('click', function (e) {
            e.preventDefault();

            window.id = $(this).parent().data('id');
            window.element = $(this).parent().parent();

            swal({
                type: "warning",
                title: "¿Estás seguro de eliminar este bloqueo?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa fa-thumbs-up"></i> Hazlo!'
            }).then((result) => {
                if (result.value)
                {
                    ajaxRemoveDt();
                }
                else
                {
                    window.id = null;
                    window.element = null;
                }
            });
        });
    }

    function ajaxRemoveDt()
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: "{{ route('crew.ajaxDestroyEventBlock') }}",
            data: {id: id},
            success: function (response) {
                if (response)
                {
                    swal({
                        type: 'success',
                        title: "Bloqueo eliminado con éxito!",
                        timer: 2000
                    });
                    
                    element.fadeOut('slow');
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal al intentar eliminar el bloqueo.", 'error');
                }

                window.id = null;
                window.element = null;
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }
</script>