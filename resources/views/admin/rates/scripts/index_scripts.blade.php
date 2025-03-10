<script type="text/javascript">
    @if (session('message'))
        swal({
            type: "{{ session('m_status') }}",
            title: "{{ session('message') }}",
            timer: 1500
        });
    @endif

    $('#datatable_rates').find('tr').on('mouseenter', function () {
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
                title: "¿Estás seguro de eliminar esat tarifa?",
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
            url: "{{ route('rates.ajaxDestroy') }}",
            data: {id: id},
            success: function (response) {
                if (response)
                {
                    swal({
                        type: 'success',
                        title: "Tarifa eliminado con éxito!",
                        timer: 2000
                    });
                    
                    element.fadeOut('slow');
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal al intentar eliminar la tarifa.", 'error');
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