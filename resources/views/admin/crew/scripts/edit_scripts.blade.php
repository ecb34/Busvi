<script type="text/javascript">
    @if (session('message'))
        swal({
            type: "{{ session('m_status') }}",
            title: "{{ session('message') }}",
            timer: 1500
        });
    @endif
    
    $('.btn-remove').on('click', function (e) {
        e.preventDefault();
        
        $('#deleteItem').submit();
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

    $('.modal').find('button.change-pass').on('click', function (e) {
        e.preventDefault();

        var form = $(this).parents('.modal').find('form');

        changePass(form);
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

    function changePass(form)
    {
        swal({
            title: "Modificar Contraseña",
            text: "¿Esta seguro que desea modificar esta contraseña?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-thumbs-up"></i> Hazlo!'
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
                if (response == 1)
                {
                    swal({
                        type: 'success',
                        title: "Contraseña modificada con éxito!",
                        timer: 1500
                    });

                    form.find('input[name="password"]').val('');
                    form.find('input[name="password_confirmation"]').val('');

                    form.parents('.modal').modal('hide');
                }
                else if (response == -1)
                {
                    console.log(response)
                    swal('Oops...', "La contraseña y su validación deben de ser iguales y no estar vacías.", 'error');
                }
                else
                {
                    console.log(response)
                    swal('Oops...', "Algo ha ido mal al intentar modificar la contraseña.", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }
</script>