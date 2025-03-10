<div class="modal fade" id="galleryModalEdit" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            {!! Form::open(['route' => ['web.gallery_edit'], 'method' => 'POST', 'files' => true]) !!}
                <input type="hidden" name="post_id" value="<?=$post->id?>">
                <input type="hidden" name="media_id" value="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Editar Imagen de la Galería</h4>
				</div>
				<div class="modal-body">
					<div class="row-fluid">
						<div class="col-xs-12">
							<div class="form-group">
								{!! Form::label('title', 'Título:') !!}
								{!! Form::text('title', null, ['class' => 'form-control']) !!}
							</div>
							<div class="form-group">
								{!! Form::label('link', 'Enlace:') !!}
								{!! Form::text('link', null, ['class' => 'form-control']) !!}
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Guardar</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>