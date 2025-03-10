<!-- Modal -->
<div class="modal fade" id="modalEvent" tabindex="-1" role="dialog" aria-labelledby="modalEventLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{!! Form::open(['route' => ['calendar.store'], 'method' => 'POST']) !!}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalEditPassLabel">Añadir Cita</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<!-- Password Field -->
							<div class="form-group">
							    {!! Form::label('start_date', 'Hora:') !!}
							    {{ Form::select('start_date', [], null, ['class' => 'form-control', 'required' => 'required', 'form' => 'createEvent']) }}
					            <?php /* <div class='input-group date'>
					                {!! Form::text('start_date', null, ['class' => 'form-control', 'required' => 'required']) !!}
					                
					                <span class="input-group-addon">
					                    <span class="glyphicon glyphicon-calendar"></span>
					                </span>
					            </div> */?>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary pull-left add-time">Guardar</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
