<div class="services_content">
    @if (Auth::user()->role != 'admin')
        <!-- Company Field -->
        <div class="form-group">
            {!! Form::label('company_id', 'Negocio') !!}
            {!! Form::select('company_id', $companies, null, ['class' => 'form-control', 'placeholder' => 'Escoja el negocio', 'required' => 'required']) !!}
        </div>
    @endif
</div>