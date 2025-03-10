<!-- Modal -->
<div class="modal fade" id="modalInformeFichaje" tabindex="-1" role="dialog" aria-labelledby="modalInformeFichajeLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalInformeFichajeLabel">Generar informe de fichajes</h4>
			</div>
			{!! Form::open(['route' => ['crew.informeFichajes'], 'method' => 'POST']) !!}
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						
						<input type="hidden" name="crew_id" value="<?=$user->id?>">

						<div class="form-group col-sm-6">
							{!! Form::label('fecha_inicio', 'Fecha de inicio:') !!}
							{!! Form::date('fecha_inicio', date('Y-m-d', strtotime('first day of this month')), ['class' => 'form-control', 'required' => 'required']) !!}
						</div>

						<div class="form-group col-sm-6">
							{!! Form::label('fecha_fin', 'Fecha de fin:') !!}
							{!! Form::date('fecha_fin', date('Y-m-d', strtotime('last day of this month')), ['class' => 'form-control', 'required' => 'required']) !!}
						</div>

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary pull-left generar">Generar informe</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>