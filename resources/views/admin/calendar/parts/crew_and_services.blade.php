
<div class="crew col-xs-12">
	<div class="row">
		<div class="col-xs-12 m-t-35">
			<ul class="nav nav-pills nav-justified">
				<li role="presentation" class="active">
					<a href="#" data-id="0">Todos</a>
				</li>
				@foreach ($services as $service)
					<li role="presentation">
						<a href="#" data-id="{{ $service->id }}">{{ $service->name }}</a>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	<div class="row">
		@foreach ($crew as $element)
			<div class="col-xs-12 m-t-35 crew-element">
				<div class="row">
					<div class="col-xs-3 text-center h-100">
						<span class="star">
							<a href="#" class="add_crew_fav" data-id="{{ $element->id }}">
								<?php $class = 'fa-star-o'; ?>

								@if ($element->isFavourite()->first())
									<?php $class = 'fa-star'; ?>
								@endif

								<i class="fa {{ $class }}" aria-hidden="true"></i>
							</a>
						</span>
						<?php $img = ($element->img) ? 'crew/' . $element->img : 'user.jpg' ?>
						<img src="{{ asset('img/' . $img) }}" alt="{{ $element->name }}">
						<p>{{ $element->name }}</p>
					</div>
					<div class="col-xs-9 h-100">
						<ul>
							@foreach ($element->services as $elem)
								<li>
									<a href="#" class="getCalendar btn btn-default" data-service="{{ $elem->service_id }}" data-crew="{{ $element->id }}">
										{{ $elem->service->name }}
									</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>