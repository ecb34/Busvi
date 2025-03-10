<script type="text/javascript">
    @if (session('message'))
        swal({
            type: "{{ session('m_status') }}",
            title: "{{ session('message') }}",
        });
    @endif

    @if (Auth::user()->role == 'operadora' || Auth::user()->role == 'superadmin')
        $('#datatable_events').find('tr').on('mouseenter', function () {
            $(this).find('td').last().append('<a href="#" class="btn-rm text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>');
            setActionRemove();
        }).on('mouseleave', function () {
            $(this).find('td').last().find('.btn-rm').remove();
        });
    @endif

    $('.btn-favourite').on('click', function (e) {
        e.preventDefault();

        ajaxFavourite($(this));
    })

    function setActionRemove()
    {
        $('.btn-rm').on('click', function (e) {
            e.preventDefault();

            ajaxRemoveDt($(this).parent().data('id'), $(this).parent().parent())
        });
    }

    function ajaxRemoveDt(id, element)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: "{{ route('calendar.ajaxDestroy') }}",
            data: {id: id},
            success: function (response) {
                if (response)
                {
                    swal({
                        type: 'success',
                        title: "Cita eliminada con éxito!",
                        timer: 2000
                    });
                    
                    element.fadeOut('slow');
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal al intentar eliminar la cita.", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }

    function ajaxFavourite(element)
    {
        var id = element.parents('tr').data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: "{{ route('companies.ajaxFavourite') }}",
            data: {id: id},
            success: function (response) {
                if (response == 1)
                {
                    element.empty();
                    element.append('<i class="fa fa-heart" aria-hidden="true"></i>');
                }
                else if (response == 0)
                {
                    element.empty();
                    element.append('<i class="fa fa-heart-o" aria-hidden="true"></i>');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }
</script>