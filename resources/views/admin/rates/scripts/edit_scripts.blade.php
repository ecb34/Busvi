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

    $('.btn-cancel-img').on('click', function (e) {
        e.preventDefault();

        $('.add-img').addClass('hidden');

        $('.sector-img').parent().removeClass('hidden');
    });
</script>