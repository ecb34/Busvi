<div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            {!! Form::open(['route' => ['web.gallery_add'], 'method' => 'POST', 'files' => true]) !!}
                <input type="hidden" name="post_id" value="<?=$post->id?>">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Añadir Imagen a la Galería</h4>
				</div>
				<div class="modal-body">
					<div class="row-fluid">
						<div class="col-xs-12">
							<div class="form-group">
								{!! Form::label('image', 'Fichero de imagen:') !!}
								<input type="file" name="image" value="" class="" accept="image/x-png,image/gif,image/jpeg" required="required">
							</div>
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