<!-- Modal -->
<div class="modal fade" id="modalEditPass" tabindex="-1" role="dialog" aria-labelledby="modalEditPassLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalEditPassLabel">Modificar Contraseña</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						{!! Form::open(['route' => ['users.ajaxUpdatePass', $company->admin->id], 'method' => 'POST']) !!}
							<!-- Password Field -->
							<div class="form-group col-sm-6">
							    {!! Form::label('password', 'Contraseña:') !!}
							    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
		                        <p class="text-info">
		                        	<small>
		                        		5 carácteres como mínimo
		                        	</small>
		                        </p>
							</div>

							<!-- Password Field -->
							<div class="form-group col-sm-6">
							    {!! Form::label('confirm_password', 'Confirmar contraseña:') !!}
							    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
							</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary pull-left change-pass">Guardar</button>
			</div>
		</div>
	</div>
</div>