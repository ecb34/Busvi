<!-- Modal -->
<div class="modal fade" id="modalStripeDays" tabindex="-1" role="dialog" aria-labelledby="modalStripeDaysLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{{ Form::open(['route' => ['stripe.status', $company->id], 'method' => 'POST', 'id' => 'payment-form']) }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalEditPassLabel">Confirmar Pago</h4>
				</div>
				<div class="modal-body">
					<div class="creditCardForm">
						<div class="payment">
							<div class="form-group">
								<label for="cvv">Cantidad</label>
								<div class="input-group">
									<input type="text" class="form-control" name="amount" value="{{ number_format($premium->amount * 1.21, 2, '.', '') }}" readonly>
									<span class="input-group-addon">€</span>
								</div>
							</div>
							<div class="form-group CVV">
								<label for="cvv">CVV</label>
								<input type="text" class="form-control" id="cvv" name="cvv">
							</div>
							<div class="form-group" id="card-number-field">
								<label for="cardNumber">Tarjeta</label>
								<input type="text" class="form-control" id="cardNumber" name="card">
							</div>
							<div class="form-group" id="expiration-date">
								<label>Expiration Date</label>
								<select name="month">
									<option value="01">January</option>
									<option value="02">February </option>
									<option value="03">March</option>
									<option value="04">April</option>
									<option value="05">May</option>
									<option value="06">June</option>
									<option value="07">July</option>
									<option value="08">August</option>
									<option value="09">September</option>
									<option value="10">October</option>
									<option value="11">November</option>
									<option value="12">December</option>
								</select>
								<select name="year">
									<option value="19"> 2019</option>
									<option value="20"> 2020</option>
									<option value="21"> 2021</option>
									<option value="22"> 2022</option>
									<option value="23"> 2023</option>
									<option value="24"> 2024</option>
									<option value="25"> 2025</option>
									<option value="26"> 2026</option>
									<option value="27"> 2027</option>
								</select>
							</div>
							<div class="form-group" id="credit_cards">
								<img src="{{ asset('lib/simple-credit-card-validation-form/assets/images/visa.jpg') }}" id="visa">
								<img src="{{ asset('lib/simple-credit-card-validation-form/assets/images/mastercard.jpg') }}" id="mastercard">
								<img src="{{ asset('lib/simple-credit-card-validation-form/assets/images/amex.jpg') }}" id="amex">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary pull-left">Guardar</button>
				</div>
			{{ Form::close() }}
		</div>
	</div>
</div>