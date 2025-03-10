<!-- Modal -->
<div class="modal fade" id="modalImportTags" tabindex="-1" role="dialog" aria-labelledby="modalImportTagsLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{!! Form::open(['route' => ['companies.importTags', $company->id], 'method' => 'POST', 'id' => 'importTags', 'files' => true]) !!}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalEditPassLabel">Importar Etiquetas</h4>
				</div>
				<div class="modal-body">
				        {{ Form::file('csv', ['class' => 'form-control', 'accept' => '.xlsx, .xls, .csv']) }}
				        {{ Form::hidden('company_id', $company->id) }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary pull-left">Guardar</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>