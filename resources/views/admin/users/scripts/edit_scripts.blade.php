<script type="text/javascript">
    @if (session('message'))
        swal({
            type: "{{ session('m_status') }}",
            title: "{{ session('message') }}",
            timer: 1500
        });
    @endif

    @if (Auth::user()->role == 'user')
        @if ($script)
            @if (session('message'))
                swal({
                    type: "{{ session('m_status') }}",
                    title: "{{ session('message') }}",
                    timer: 1500
                });
            @endif
        @endif
    @endif

    $('#datetimepicker1').datetimepicker({
        locale: 'es',
        format: 'DD-MM-YYYY HH:mm',
        disabledDates: [
                "04/13/2018 18:40"
            ]
    });
    
    $('.btn-remove').on('click', function (e) {
        e.preventDefault();
        
        $('#deleteItem').submit();
    });

    // $('.btn-favourite').on('click', function (e) {
    //     e.preventDefault();

    //     ajaxFavourite($(this));
    // })

    $('.modal').find('button.change-pass').on('click', function (e) {
        e.preventDefault();

        var form = $(this).parents('.modal').find('form');

        changePass(form);
    });

    $('#datatable_events').find('tr').on('mouseenter', function () {
        $(this).find('td').last().append('<a href="#" class="btn-rm-user text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>');
        setActionRemove();
    }).on('mouseleave', function () {
        $(this).find('td').last().find('.btn-rm-user').remove();
    });

    $('.item-img').parent().on('mouseenter', function () {
        $(this).prepend('<span class="text-danger span-close"><i class="fa fa-times" aria-hidden="true"></i></span>');

        $('.span-close').on('click', function (e) {
            e.preventDefault();

            $(this).parent().addClass('hidden');

            $('.add-img').removeClass('hidden');
        });
    }).on('mouseleave', function () {
        $('.span-close').remove();
    });

    $('.btn-cancel-img').on('click', function (e) {
        e.preventDefault();

        $('.add-img').addClass('hidden');

        $('.item-img').parent().removeClass('hidden');
    });

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

    function setActionRemove()
    {
        $('.btn-rm-user').on('click', function (e) {
            e.preventDefault();
        
            if (confirm("¿Estas seguro que quieres eliminar esto?"))
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
            url: "{{ route('calendar.ajaxDestroy') }}",
            data: {id: id},
            success: function (response) {
                console.log(response)
                if (response == '1')
                {
                    swal({
                        type: 'success',
                        title: "Eliminación con éxito.",
                        timer: 2000
                    });
                    
                    element.fadeOut('slow');
                }
                else if (response == '-1')
                {
                    swal({
                        type: 'warning',
                        title: "No ha sido posible realizar la eliminación.",
                        timer: 6000
                    });
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal!", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }

    function changePass(form)
    {
        swal({
            title: "{{ trans('app.admin.users.user_change_pass') }}",
            text: "{{ trans('app.admin.users.user_something_wrong_text') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-thumbs-up"></i> {{ trans('app.common.do_it') }}!'
        }).then((result) => {
            if (result.value)
            {
                ajaxChangePass(form);
            }
        });
    }

    function ajaxChangePass(form)
    {
        var url = form.attr('action');
        var pass = form.find('input[name="password"]').val();
        var repass = form.find('input[name="password_confirmation"]').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: url,
            data: {pass: pass, repass: repass},
            success: function (response) {
                console.log(response)
                if (response == 1)
                {
                    swal({
                        type: 'success',
                        title: "{{ trans('app.admin.users.user_modify_pass_success') }}",
                        timer: 1500
                    });

                    form.parents('.modal').modal('hide');
                }
                else if (response == -1)
                {
                    swal('Oops...', "{{ trans('app.admin.users.user_no_equal_pass') }}", 'error');
                }
                else
                {
                    swal('Oops...', "{{ trans('app.admin.users.user_modify_pass_error') }}", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }

    // function ajaxFavourite(element)
    // {
    //     var id = element.parents('tr').data('id');

    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    //         }
    //     });
    //     $.ajax({
    //         method: "POST",
    //         url: "{{ route('companies.ajaxFavourite') }}",
    //         data: {id: id},
    //         success: function (response) {
    //             if (response == 1)
    //             {
    //                 element.empty();
    //                 element.append('<i class="fa fa-heart" aria-hidden="true"></i>');
    //             }
    //             else if (response == 0)
    //             {
    //                 element.empty();
    //                 element.append('<i class="fa fa-heart-o" aria-hidden="true"></i>');
    //             }
    //         }
    //     })
    //     .fail(function( jqXHR, textStatus ) {
    //         console.log( jqXHR );
    //         console.log( "Request failed: " + textStatus );
    //     });
    // }
</script>