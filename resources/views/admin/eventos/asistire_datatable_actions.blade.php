@if(!$pagado == 1)
	@php
		$evento = \App\Evento::find($evento_id);
		$plazas_libres = !is_null($evento) ? $evento->plazas_libres : 0;
	@endphp
	@if($plazas_libres < $plazas_reservadas)
		<button title="Las entradas se han agotado antes de que pudieras completar tu pago" class="btn btn-danger btn-xs disabled">Entradas agotadas</button>
	@else
		<a href="{{route('eventoPaypal', $id)}}" class="btn btn-primary btn-xs">Paypal<i class="glyphicon glyphicon-eur"> </i> </a>
		<a href="#" onclick="pagar_stripe({{$id}}, {{$precio * $plazas_reservadas}})" class="btn btn-danger btn-xs">Tarjeta<i class="glyphicon glyphicon-eur"> </i> </a>
	@endif
@else
	<button title="Todo correcto!" class="btn btn-primary btn-xs disabled">Ya estas apuntad@</button>
@endif
