<script>
    $(document).ready(function(){
        
        $('.btn-gallery-modal').click(function(){
            $('#galleryModal').modal('show');
        });

        $('.btn-edit').click(function(){
            $('#galleryModalEdit input[name="media_id"]').val($(this).attr('data-id'));
            $('#galleryModalEdit input[name="title"]').val($(this).attr('data-title'));
            $('#galleryModalEdit input[name="link"]').val($(this).attr('data-link'));
            $('#galleryModalEdit').modal('show');
        });

        $('.btn-remove-gallery-image').click(function(){
            document.gallery_delete.media_id.value = $(this).attr('data-name');
            document.gallery_delete.submit();
        });

        $('.btn-orden').click(function(){
            document.gallery_order.media_id.value = $(this).attr('data-name');
            document.gallery_order.accion.value = $(this).attr('data-accion');
            document.gallery_order.submit();
        });

    })
</script>