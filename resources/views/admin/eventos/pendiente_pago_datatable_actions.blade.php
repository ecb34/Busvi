<a  href="#" onclick="pagar_paypal({{$id}})" href="{{route('eventoPaypal', $id)}}" class="btn btn-primary btn-xs">Paypal<i class="glyphicon glyphicon-eur"> </i> </a>
<a href="#" onclick="pagar_stripe({{$id}}, {{$precio_final}})" class="btn btn-danger btn-xs">Tarjeta<i class="glyphicon glyphicon-eur"> </i> </a>
