<a href="{{route('eventos.edit', [$id])}}" class="btn btn-primary btn-xs">Info</a>
@if($pagado_a_comercio == 0)
 	<a href="{{route('admin.eventos.marcarEventoPagado', [$id])}}" class="btn btn-primary btn-xs">Marcar Pagado <i class="glyphicon glyphicon-eur"> </i> </a> 
@else
	<a href="#" class="btn btn-primary btn-xs" disabled>Pagado <i class="glyphicon glyphicon-eur"> </i> </a> 
@endif