<!-- Modal -->
<div class="modal fade" id="modalEditFichaje" tabindex="-1" role="dialog" aria-labelledby="modalEditFichajeLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalEditFichajeLabel">Modificar Fichaje</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						
						<input type="hidden" name="fichaje_id" value="">

						<div class="form-group col-sm-6">
							{!! Form::label('fecha_inicio', 'Fecha de inicio:') !!}
							{!! Form::date('fecha_inicio', '', ['class' => 'form-control', 'required' => 'required']) !!}
						</div>

						<div class="form-group col-sm-6">
							{!! Form::label('hora_inicio', 'Hora de inicio:') !!}
							{!! Form::time('hora_inicio', '', ['class' => 'form-control', 'required' => 'required']) !!}
						</div>

						<div class="form-group col-sm-12" style="margin-bottom: 10px;">
							<div class="checkbox">
								<label>
									<input checked="checked" name="fichaje_cerrado" type="checkbox" value=""> Fichaje cerrado
								</label>
							</div>
						</div>

						<div class="form-group col-sm-6 fin">
							{!! Form::label('fecha_fin', 'Fecha de fin:') !!}
							{!! Form::date('fecha_fin', '', ['class' => 'form-control', 'required' => 'required']) !!}
						</div>

						<div class="form-group col-sm-6 fin">
							{!! Form::label('hora_fin', 'Hora de fin:') !!}
							{!! Form::time('hora_fin', '', ['class' => 'form-control', 'required' => 'required']) !!}
						</div>
						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary pull-left guardar">Guardar</button>
			</div>
		</div>
	</div>
</div>