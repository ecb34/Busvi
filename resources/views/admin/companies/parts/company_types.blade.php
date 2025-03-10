<div class="col-xs-12">
    <!-- Name Field -->
    <div class="form-group">
        {!! Form::label('type', 'Tipo de Empresa *:') !!}
        {!! Form::select('type', $types_company, $val_types_company ?? '', ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
</div>