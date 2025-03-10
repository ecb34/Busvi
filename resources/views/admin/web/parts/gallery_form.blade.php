<div class="form-group col-sm-12">
    {!! Form::label('galeria', 'Galería de imágenes:') !!}
</div>

<div class="col-xs-12">
    <table class="table table-striped table-hover table-responsive data-table imagenes">
        <thead>
            <th>Nombre</th>
            <th width="5%"></th>
        </thead>
        <tbody>
            @foreach ($post->media as $media)
            <tr data-id="{{ $media->id }}">
                <td>
                    <a class="btn btn-xs btn-primary btn-orden" data-accion="bajar-todo" data-name="{{ $media->id }}" data-id="{{ $media->id }}">
                        <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-xs btn-primary btn-orden" data-accion="bajar" data-name="{{ $media->id }}" data-id="{{ $media->id }}">
                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-xs btn-primary btn-orden" data-accion="subir" data-name="{{ $media->id }}" data-id="{{ $media->id }}">
                        <i class="fa fa-angle-up" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-xs btn-primary btn-orden" data-accion="subir-todo" data-name="{{ $media->id }}" data-id="{{ $media->id }}">
                        <i class="fa fa-angle-double-up" aria-hidden="true"></i>
                    </a>

                    <a class="btn btn-xs btn-primary btn-edit" data-name="{{ $media->id }}" data-id="{{ $media->id }}" data-title="{{ isset($media->custom_properties['title']) ? $media->custom_properties['title'] : '' }}" data-link="{{ isset($media->custom_properties['link']) ? $media->custom_properties['link'] : '' }}" style="margin-left: 15px">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>

                    <a href="{{ $media->getFullUrl() }}" target="_blank">
                        <img src="{{ $media->getFullUrl() }}" height="80px" style="margin: 0 15px;">
                    </a>
                    
                </td>
                <td width="10%" class="text-right">
                    <a href="#" class="btn btn-sm text-danger btn-remove-gallery-image" data-name="{{ $media->id }}">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
            @endforeach

            @if(count($post->media) == 0)
            <tr data-id="">
                <td colspan="2" class="text-center" style="padding: 40px 0;">
                    no hay ninguna imagen
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<form action="{{ route('web.gallery_delete') }}" method="post" name="gallery_delete">
    {{ csrf_field() }}
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    <input type="hidden" name="media_id" value="">
</form>

<form action="{{ route('web.gallery_order') }}" method="post" name="gallery_order">
    {{ csrf_field() }}
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    <input type="hidden" name="media_id" value="">
    <input type="hidden" name="accion" value="">
</form>

<!-- Name Field -->
<div class="form-group col-sm-12">
    <a href="#" class="btn btn-primary btn-gallery-modal pull-right">Añadir imagen</a>    
</div>