<script src="{{ asset('lib/iban.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    @if (session('message'))
        swal({
            type: "{{ session('m_status') }}",
            title: "{{ session('message') }}",
            timer: 2500
        });
    @endif

    $('input[name^="horario_"').datetimepicker({
        format: 'HH:mm'
    });
    
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

    $('#modalEditPass').find('button.change-pass').on('click', function (e) {
        e.preventDefault();

        var form = $(this).parents('.modal').find('form');

        changePass(form);
    });

    /*$('input[name="bank_count"]').on('change keyup', function (e) {
        console.log($('input[name="card_number"]').val() == null && $('input[name="card_number"]').prop('required'))
        if ($(this).val() == '')
        {
            $('input[name="card_number"]').prop('required', true);
        }
        else
        {
            $('input[name="card_number"]').prop('required', false);
        }
    });

    $('input[name="card_number"]').on('change keyup', function (e) {
        if ($(this).val() == '')
        {
            $('input[name="bank_count"]').prop('required', true);
        }
        else
        {
            $('input[name="bank_count"]').prop('required', false);
        }
    });*/

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

    $('input[name="tags"]').tagsinput();

    $('.btn-gallery-modal').on('click', function (e) {
        e.preventDefault();

        $('#galleryModal').modal('show');
    });

    $('input[name="bank_count"]').on('keyup change', validar_bank_count);

    function validar_bank_count(){
        if (IBAN.isValid($(this).val()) || $(this).val() == '' || $(this).val() == '0')
        {
            $('#companiesForm').find('input[type="submit"]').prop('disabled', false);
            $(this).css('box-shadow', 'inherit');
        }
        else
        {
            $('#companiesForm').find('input[type="submit"]').prop('disabled', true);
            $(this).css('box-shadow', '0 0 10px red');
        }
    }

    $(document).ready(function(){
        $('input[name="bank_count"]').trigger('change');
    });

    $('.btn-blocked').on('click', function (e) {
        e.preventDefault();

        var title = $(this).text();

        swal({
            title: title,
            text: "¿Esta seguro que desea realizar esta acción?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-thumbs-up"></i> Hazlo!'
        }).then((result) => {
            if (result.value)
            {
                ajaxBlockCompany($(this).data('val'))
            }
        });
    });

    $('.btn-get-down').on('click', function (e) {
        e.preventDefault();

        swal({
            title: 'Solicitar Baja',
            text: "¿Esta seguro que desea solicitar la baja?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-thumbs-up"></i> Hazlo!'
        }).then((result) => {
            if (result.value)
            {
                ajaxGetDown();
            }
        });
    });

    $(window).on('load', function () {
        $('.btn-remove-gallery-image').on('click', function (e) {
            e.preventDefault();

            swal({
                type: "warning",
                title: "¿Estás seguro de eliminar esta imagen?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa fa-thumbs-up"></i> Hazlo!'
            }).then((result) => {
                if (result.value)
                {
                    $('input[name="image_name"]').val($(this).data('name'));

                    $('#removeImageGallery').submit();
                }
            });
        });
    });

    $('input[type="submit"]').on('click', function () {
        $('body').prepend('<div class="overlay"></div>');

        $.each($('#companiesForm').find('input,select,textarea'), function () {
            if ($(this).prop('required') && ($(this).val() == '' || $(this).val() == null))
            {
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
                if (response != 0)
                {
                    swal({
                        type: 'success',
                        title: "Contraseña modificada con éxito!",
                        timer: 1500
                    });

                    form.parents('.modal').modal('hide');
                }
                else if (response == -1)
                {
                    swal('Oops...', "La contraseña y su validación deben de ser iguales y no estar vacías.", 'error');
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal al intentar modificar la contraseña.", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }

    function ajaxBlockCompany(val)
    {
        var id = {{ $company->id }};

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: "{{ route('companies.block') }}",
            data: {val: val, id: id},
            success: function (response) {
                if (response != 0)
                {
                    location.reload();
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal al realizar la acción.", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }

    function ajaxGetDown()
    {
        var id = {{ $company->id }};

        $('body').prepend('<div class="overlay"></div>');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: "{{ route('companies.getDown') }}",
            data: {id: id},
            success: function (response) {
                if (response != 0)
                {
                    $('.overlay').remove();
                    
                    swal({
                        type: 'success',
                        title: "Su baja ha sido solicitada correctamente. En breve será efectiva."
                    });
                }
                else
                {
                    swal('Oops...', "Algo ha ido mal al realizar la acción.", 'error');
                }
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log( jqXHR );
            console.log( "Request failed: " + textStatus );
        });
    }

    // orden de la galeria

    $(document).ready(function(){

        $('.btn.btn-orden').click(function(){
            
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var accion = $(this).attr('data-accion');
            
            $.post('{{ route('companies.orderGallery') }}', {
                _token: '{{ csrf_token() }}',
                company_id: {{ $company->id }},
                id: id,
                accion: accion
            }, function(data){

                var filas = $('table.imagenes tr').detach();
                filas.sort(function(a, b){

                    var orden_a = 0;
                    var orden_b = 0;

                    for(var i = 0; i < data.length; i++){
                        if(parseInt($(a).attr('data-id')) == data[i].id){
                            orden_a = data[i].order;
                        }
                        if(parseInt($(b).attr('data-id')) == data[i].id){
                            orden_b = data[i].order;
                        }
                    }

                    return orden_a - orden_b;

                });
                filas.appendTo('table.imagenes tbody');

            });

        });

        $('.btn.btn-offer').click(function(){
            var id = $(this).attr('data-id');
            
            $.post('{{ route('companies.offerGallery') }}', {
                _token: '{{ csrf_token() }}',
                company_id: {{ $company->id }},
                id: id,
            }, function(data){

                if(data.offer){
                    $('.btn.btn-offer[data-id="' + data.id +'"]').removeClass('btn-default').addClass('btn-success');
                } else {
                    $('.btn.btn-offer[data-id="' + data.id +'"]').removeClass('btn-success').addClass('btn-default');
                }

            });

        });

        $('.btn.btn-edit-gallery').click(function(){
            $('#galleryEditModal input[name="id"]').val($(this).attr('data-id'));
            $('#galleryEditModal input[name="description"]').val($(this).attr('description'));
            $('#galleryEditModal').modal('show');
        });

    });

</script>