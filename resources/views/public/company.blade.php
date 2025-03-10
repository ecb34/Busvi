@extends('layouts.web')

@section('content')
    <!-- Breadcrumbs
    ============================================ -->
    <div class="page-title-social">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title float-left">
                    	<h2>{{ $company->name_comercial }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<!-- Single Blog Page Area
	============================================ -->
	<div class="blog-page-area margin-bottom-100">
		<div class="container">
			<div class="row">
				<div class="blogs col-xs-12">
					<!-- Single Blog -->
					<div class="sin-blog">
						<div class="content">
							<div class="row">
								<div class="col-xs-12 col-sm-4">
									<img src="{{ asset('img/companies/' . $company->logo) }}" alt="{{ $company->name_comercial }}" class="img-responsive">
                  					<div id="map"></div>
								</div>

								<div class="col-xs-12 col-sm-8 margin-bottom-100">
									<div class="row">
										<div class="col-xs-12">
											<div class="row">
												<div class="col-md-4">	
													<ul class="datos_empresa">
														<li><label>{{ trans('app.common.company') }}:</label> <strong class="text-info">{{ $company->name_comercial }}</strong></li>
														<li><label>{{ trans('app.common.sector') }}:</label> <strong class="text-info">{{ $company->sector->name }}</strong></li>
														<?php if(!is_null($company->phone) && $company->phone != ''){ ?>
														<li><label>{{ trans('app.common.phone') }}:</label> <a href="tel:{{ $company->phone }}" class="text-info counter" company_id="<?=$company->id?>" variable="phone">{{ $company->phone }}</a></li>
														<?php } ?>
														<?php if(!is_null($company->phone2) && $company->phone2 != ''){ ?>
														<li><label>{{ trans('app.common.phone') }} 2:</label> <a href="tel:{{ $company->phone2 }}" class="text-info counter" company_id="<?=$company->id?>" variable="phone">{{ $company->phone2 }}</a></li>
														<?php } ?>
														<?php if(!is_null($company->web) && $company->web != ''){ ?>
														<li><label>{{ trans('app.common.web') }}:</label> <a href="{{ $company->web }}" class="text-info counter" company_id="<?=$company->id?>" variable="web" target="_blank">{{ $company->web }}</a></li>
														<?php } ?>
		                    							@if (!is_null($company->admin))
														<li><label>{{ trans('app.common.email') }}:</label> <a href="mailto:{{ $company->admin->email }}" class="text-info counter" company_id="<?=$company->id?>" variable="email">{{ $company->admin->email }}</a></li>
		                    							@endif
														<li><label>{{ trans('app.common.address') }}:</label> <strong class="text-info">{{ $company->address }}</strong></li>
														<li><label>{{ trans('app.common.city') }}:</label> <strong class="text-info">{{ $company->city }}</strong></li>
														<li><label>{{ trans('app.common.province') }}:</label> <strong class="text-info">{{ $company->province }}</strong></li>
													</ul>

													<strong>{{ trans('app.common.schedule') }}</strong>
													<?php if($company->open_now()){ ?>
													<div class="text-success open-now"><strong>{{ strtoupper(trans('app.common.open_now')) }}</strong></div>
													<?php } else { ?>
													<div class="text-danger open-now"><strong>{{ strtoupper(trans('app.common.closed_now')) }}</strong></div>
													<?php } ?>
													@include('public.parts.schedule_days')

												</div>
												<div class="col-md-8">
													
													<strong>{{ trans('app.common.services') }}:</strong>
													<ul class="servicios">
														@foreach ($company->services as $service)
															<li>
																<strong class="text-info">{{ $service->name }}</strong>
																<small>{{ trans('app.common.price') }}: <strong>{{ $service->price }} € </strong></small>
																<small>{{ trans('app.common.duration') }}: <strong>{{ $service->min }} min.</strong></small>
															</li>
														@endforeach
													</ul>

													<div style="clear: both"></div>

													@if (Auth::check() && Auth::user()->role == 'user' &&
														$company->type == 1 && $company->enable_events == 1)
													<div style="margin-top: 20px;">
														<a href="{{ route('calendar.goToCreate', $company->id) }}" class="button blue big icon" style="display: inline-block; float: none;">
															{{ trans('app.common.ask_for_a_date') }} <i class="fa fa-calendar"></i>
														</a>
													</div>
													@endif

													@if (Auth::check() && Auth::user()->role == 'user' &&
														$company->type == 1 && $company->enable_reservas == 1)
													<div style="margin-top: 20px;">
														<a class="solicitar_reserva button blue big icon" style="display: inline-block; float: none;">
															{{ trans('app.reservas.solicitar_reserva') }} <i class="fa fa-calendar"></i>
														</a>
													</div>
													@endif

													@if (Auth::check() && Auth::user()->role == 'user' &&
														$company->type == 1 && $company->accept_cheque_regalo == 1)
													<div style="margin-top: 20px;">
														<a class=" button blue big icon" style="display: inline-block; float: none;" href="{!! url('admin/cheques_regalo/create/'.$company->id) !!}">
															{{ trans('app.cheques_regalo.comprar_cheque') }} <i class="fa fa-gift"></i>
														</a>
													</div>
													@endif

												</div>
											</div>	
										</div>
										<div class="col-xs-4 text-right">
											@if (Auth::check() && Auth::user()->role == 'user')
												<a href="{{ route('exit') }}" class="button red-light icon set-favorite" data-id="{{ $company->id }}">
													@if ($company->isFavourite)
														{{ trans('app.common.favourite') }} <i class="fa fa-heart"></i>
													@else
		                                            	{{ trans('app.common.mark_favourite') }} <i class="fa fa-heart-o"></i>
													@endif
	                                        	</a>
											@endif
										</div>
									</div>
								</div>

								<div class="col-xs-12">
									<div class="row">
				                        <div class="recently-added-slider">
				                            @foreach ($company->gallery as $element)
				                                <div class="sin-added-item gallery">
				                                	<a data-fancybox="gallery" href="{{ asset('/img/companies/galleries/' . $company->id . '/original/' . $element->filename) }}">
														<img src="{{ asset('/img/companies/galleries/' . $company->id . '/thumb/' . $element->filename) }}" alt="{{ $company->name_comercial }}" width="100%">
														<?php if($element->offer != '') { ?>
														<button class="btn btn-warning btn-offer">OFERTA</button>
														<?php } ?>
													</a>
													<?php if($element->description != '') { ?>
													<p class="gallery-description"><?=htmlentities($element->description)?></p>
													<?php } ?>
				                                </div>
				                            @endforeach
				                        </div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw&libraries=places"></script>
    @include('public.scripts.company_map')
	<script type="text/javascript">
	    
		$('.set-favorite').on('click', function (e) {
			e.preventDefault();

        	ajaxFavourite($(this));
		});

		// $(window).on('load', function () {
		// 	myMap();
		// });

		// function myMap()
		// {
  //           var point = new google.maps.LatLng({{ $company->lat }}, {{ $company->long }});

		//     var mapOptions = {
		//         center: point,
		//         zoom: 15,
		//         // mapTypeId: google.maps.MapTypeId.HYBRID
		//         mapTypeId: google.maps.MapTypeId.ROADMAP
		//     }
		// 	var map = new google.maps.Map(document.getElementById("map"), mapOptions);

  //           var marker = new google.maps.Marker({
  //               position: point,
  //               map: map,
  //               icon: "{{ asset('wyzi/img/marker') }}" + '/dot.png'
  //           });
		// }

		function ajaxFavourite(element)
	    {
	        var id = element.data('id');

	        $.ajaxSetup({
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
	            }
	        });
	        $.ajax({
	            method: "POST",
	            url: "{{ route('companies.ajaxFavourite') }}",
	            data: {id: id},
	            success: function (response) {
	                if (response == 1)
	                {
	                    element.empty();
	                    element.append('{{ trans("app.common.favourite") }} <i class="fa fa-heart"></i>');
	                }
	                else if (response == 0)
	                {
	                    element.empty();
	                    element.append('{{ trans("app.common.mark_favourite") }} <i class="fa fa-heart-o"></i>');
	                }
	            }
	        })
	        .fail(function( jqXHR, textStatus ) {
	            console.log( jqXHR );
	            console.log( "Request failed: " + textStatus );
	        });
	    }

		$(document).ready(function(){
			
			$('.solicitar_reserva').click(function(){
				$('#modalReserva input[name="plazas"]').val(1);
				$('#modalReserva input[name="fecha"]').val('<?=trans('app.reservas.selecciona_fecha_turno')?>');
				$('#modalReserva').modal('show');
			});

			$('#calendario_turnos').fullCalendar({
				events: '<?=action('Admin\ReservasController@getTurnosDisponibles', $company->id)?>',
				firstDay: 1,                
				defaultView: 'month',
				header: {
					left: 'prev,next',
					center: 'title',
					right: 'today'
				},
				validRange: function(nowDate) {
					return {
						start: nowDate.startOf('day'),
						end: nowDate.clone().add(6, 'months')
					};
				},
				height: 'auto',
				eventClick: function(info) {
					$('#modalReserva input[name="turno_id"]').val(info.turno_id);
					$('#modalReserva input[name="fecha_text"]').val(info.start.format('D/M/YY') + ' - ' + info.title_notags);
					$('#modalReserva input[name="fecha"]').val(info.start.format('YYYY-MM-DD'));	
				},
				eventRender: function(event, element, view){
                    var html = '<div>' + event.title + '</div>';
                    $(element).find('.fc-title').html(html);
				},
				eventOrder: 'inicio',
			});

		});

	</script>
@endsection

@section('modals')
@if (Auth::check() && Auth::user()->role == 'user' && $company->type == 1 && $company->enable_reservas == 1)
<div id="modalReserva" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form action="<?=\URL::action('Admin\ReservasController@postReserva')?>" method="post">
				<?=csrf_field()?>
				<input type="hidden" name="company_id" value="<?=$company->id?>">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-2">

							<div class="form-group">	
								<label for="plazas" class="control-label"><?=trans('app.reservas.plazas')?></label>
								<input type="number" class="form-control" id="plazas" name="plazas" value="1" min="1" step="1" required>
							</div>

						</div>
						<div class="col-md-10">

							<div class="form-group">	
								<label class="control-label"><?=trans('app.reservas.fecha')?></label>
								<input type="text" class="form-control" id="fecha_text" name="fecha_text" value="<?=trans('app.reservas.selecciona_fecha_turno')?>" readonly style="background: #fff">
							</div>

						</div>
					</div>

					<div class="form-group">
						<div id="calendario_turnos"></div>
						<input type="hidden" name="turno_id" value="">
						<input type="hidden" name="fecha" value="">
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('app.cerrar') }}</button>
					<button type="submit" class="btn btn-primary">{{ trans('app.reservas.solicitar_reserva') }}</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
@endsection