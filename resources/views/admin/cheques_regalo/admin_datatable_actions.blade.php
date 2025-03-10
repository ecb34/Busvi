@if($pagado_a_comercio == 0 && $status == 3)
 	<a href="{{route('admin.cheques_regalo.marcarChequeRegaloPagado', [$id])}}" class="btn btn-primary btn-xs">Marcar Pagado<i class="glyphicon glyphicon-eur"> </i> </a>
@else
	<a href="#" class="btn btn-primary btn-xs" disabled> {{$pagado_a_comercio == 0 ? 'No usado' : 'Pagado'}} <i class="glyphicon glyphicon-eur"> </i> </a>
@endif