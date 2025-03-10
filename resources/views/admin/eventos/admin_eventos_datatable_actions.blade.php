<a href="{{route('eventos.edit', [$id])}}" class="btn btn-primary btn-xs">Info</a>
@if($es_editable == 1 || \Auth::user()->role == 'admin' || \Auth::user()->role == 'superadmin')
 	<button type="button" class="btn btn-danger btn-xs borrar" data-id="{{ $id }}">Borrar<i class="glyphicon glyphicon-trash"> </i> </a>
@else
	<button title="No se puede borrar si ya hay inscritos!" class="btn btn-danger btn-xs disabled">Borrar</button>
@endif
