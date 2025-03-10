

<div class="crew">
	<div class="col-xs-12">
		<!-- Crew Field -->
		<div class="form-group">
		    {!! Form::label('crew', 'Profesional:') !!}
		    {!! Form::select('crew', $crew, null, ['class' => 'form-control crew', 'placeholder' => 'Escoja profesional...', 'required' => 'required']) !!}
		</div>
	</div>
</div>
