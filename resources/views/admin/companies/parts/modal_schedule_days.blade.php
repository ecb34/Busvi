<!-- Modal -->
<div class="modal fade" id="modalScheduleDays" tabindex="-1" role="dialog" aria-labelledby="modalScheduleDaysLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalEditPassLabel">Horarios</h4>
			</div>
			<div class="modal-body">
        		@include('admin.companies.parts.schedule_days')
        		<a href="#" class="btn btn-warning btn-copy">
        			Copiar todos como el Lunes
        		</a>
        		<p class="text-danger">
        			Después de copiar, eliminar los horarios no procedentes
        		</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary pull-left change-pass" data-dismiss="modal">Guardar</button>
			</div>
		</div>
	</div>
</div>