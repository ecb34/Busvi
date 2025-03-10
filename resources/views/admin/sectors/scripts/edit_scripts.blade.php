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
</script>