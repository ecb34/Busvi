<!-- Modal -->
<div class="modal fade" id="modalAddBlockToEvent" tabindex="-1" role="dialog" aria-labelledby="modalAddBlockToEventLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{!! Form::open(['route' => ['crew.addBlockEvent', $user->id], 'method' => 'POST']) !!}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalEditPassLabel">Añadir Blockeo en Día / Hora</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<!-- Password Field -->
							<div class="form-group">
							    {!! Form::label('start_date', 'Inicio de Bloqueo:') !!}
					            <div class='input-group date calendar'>
					                {!! Form::text('start_date', null, ['class' => 'form-control', 'required' => 'required']) !!}
					                <span class="input-group-addon">
					                    <span class="glyphicon glyphicon-calendar"></span>
					                </span>
					            </div>
							</div>

							<div class="form-group">
								{!! Form::label('all_day', 'Todo el día:') !!}
						        <div class="row">
						            <div class="col-xs-12">
						            	<div class="checkbox">
						            		<label for="">
												{!! Form::checkbox('all_day', 1, true) !!} Sí, todo el día bloqueado.
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Password Field -->
							<div class="form-group">
							    {!! Form::label('end_date', 'Fin de Bloqueo:') !!}
					            <div class='input-group date calendar-time'>
					                {!! Form::text('end_date', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
					                <span class="input-group-addon">
					                    <span class="glyphicon glyphicon-time"></span>
					                </span>
					            </div>
							</div>

							<div class="form-group">
								{!! Form::label('text', 'Texto (opcional):') !!}
						        {!! Form::text('text', null, ['class' => 'form-control']) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary pull-left add-block">Guardar</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>