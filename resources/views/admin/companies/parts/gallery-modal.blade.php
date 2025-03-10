
<div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{!! Form::open(['route' => ['company.gallery', $company->id], 'method' => 'POST', 'files' => true]) !!}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Añadir Imagen a la Galería</h4>
				</div>
				<div class="modal-body">
					<div class="row-fluid">
						<div class="col-xs-12">
							<div class="form-group">
								<input type="file" name="image" value="" class="" accept="image/x-png,image/gif,image/jpeg" required="required">
						        <p class="text-info">
						            <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG y un máximo de 50 fotos, más cantidad contactar con Busvi</small>
						        </p>
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

<div class="modal fade" id="galleryEditModal" tabindex="-1" role="dialog" aria-labelledby="galleryEditModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{!! Form::open(['route' => ['companies.editGallery'], 'method' => 'POST']) !!}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Editar Imagen</h4>
				</div>
				<div class="modal-body">
					<div class="row-fluid">
						<div class="col-xs-12">
							<div class="form-group">
								<input type="hidden" name="id" value="">
								<input type="hidden" name="company_id" value="{{ $company->id }}">
								<div class="form-group">
									{!! Form::label('description', 'Descripción:') !!}
									{!! Form::text('description', '', ['class' => 'form-control']) !!}
								</div>
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