@if($validado == 0)
	Denegado <a  href="#" onclick="validar({{$id}})" class="btn btn-primary btn-xs">Validar<i class="glyphicon glyphicon-ok"> </i> </a>
@else
	Validado  <a  href="#" onclick="denegar({{$id}})" class="btn btn-danger btn-xs">Denegar<i class="glyphicon glyphicon-remove"> </i> </a>
@endif
