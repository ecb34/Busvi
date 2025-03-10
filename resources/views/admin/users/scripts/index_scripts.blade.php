<script type="text/javascript">
    @if ($errors->any())
        @foreach ($errors->all() as $error)
	        swal({
	            type: "error",
	            title: "{{ $error }}"
	        });
        @endforeach
    @else
	    @if (session('message'))
	        swal({
	            type: "{{ session('m_status') }}",
	            title: "{{ session('message') }}"
	        });
	    @endif
	@endif

    $('#datatable_users').find('tr').on('mouseenter', function () {
        $(this).find('td').last().append('<a href="#" class="btn-rm-user text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>');
        setActionRemove();
    }).on('mouseleave', function () {
        $(this).find('td').last().find('.btn-rm-user').remove();
    });

    function setActionRemove()
    {
        $('.btn-rm-user').on('click', function (e) {
            e.preventDefault();
        
            if (confirm("{{ trans('app.admin.users.remove_user_question') }}"))
            {
                ajaxRemoveDt($(this).parent().data('id'), $(this).parent().parent())
                
            }
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
            url: "{{ route('users.ajaxDestroy') }}",
            data: {id: id},
            success: function (response) {
                console.log(response)
                if (response == '1')
                {
                    swal({
                        type: 'success',
                        title: "{{ trans('app.admin.users.user_remove_success') }}",
                        timer: 2000
                    });
                    
                    element.fadeOut('slow');
                }
                else if (response == '-1')
                {
                    swal({
                        type: 'warning',
                        title: "{{ trans('app.admin.users.user_no_able_remove') }}",
                        timer: 6000
                    });
                }
                else
                {
                    swal('Oops...', "{{ trans('app.admin.users.user_something_wrong') }}", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }
</script>