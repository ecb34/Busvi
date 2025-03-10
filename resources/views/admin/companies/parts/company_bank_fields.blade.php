
<div class="col-xs-12">
    <!-- Account Number Field -->
    <div class="form-group">
        {!! Form::label('bank_count', 'Número de Cuenta (IBAN) *:') !!}
        {!! Form::text('bank_count', null, ['class' => 'form-control', 'required' => 'required']) !!}
        <p class="text-danger">
            <small>
                Hacemos constar que la devolución del importe procedente, a través de este método de pago, llevará un recargo de 5 €. <br><br>
                La devolución de 3 importes supondría el bloqueo del negocio, y por consiguiente supondría generar un alta nueva con los gastos que ello conllevaría según perfil y tarifas vigentes.
            </small>
        </p>
    </div>

    <!-- Name Field -->
    {{-- <div class="form-group">
        {!! Form::label('card_number', 'Tarjeta de Crédito Débito **:') !!}
        <a href="#" data-toggle="tooltip" data-placement="top" title="Uno de estos dos campos debe de ser rellenado.">
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
        </a>
        {!! Form::text('card_number', null, ['class' => 'form-control', (isset($company) && (! $company->bank_count)) ? 'required' : '']) !!}
    </div> --}}
</div>