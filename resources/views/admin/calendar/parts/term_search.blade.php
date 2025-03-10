
<div class="term-searcher-container">
	@foreach ($toReturn as $key => $element)
		<h4 class="m-t-0">{{ trans('app.admin.search_special.' . $key) }}</h4>
		
		@if ($key == 'services')
			@foreach ($element as $item)
				<div class="col-xs-12 m-b-15">
					<a href="#" class="btn-special-search" data-type="{{ $key }}" data-value="{{ $item['value'] }}">
						<p><strong>({{ $item['count'] }})</strong> {{ $item['value'] }}</p>
					</a>
				</div>
				<div class="clearfix"></div>
			@endforeach
		@else
			<div class="col-xs-12 m-b-15">
				@foreach ($element as $index => $value)
					<a href="#" class="btn-special-search" data-type="{{ $key }}" data-value="{{ $index }}">
						<p><strong>({{ $value }})</strong> {{ $index }}</p>
					</a>
				@endforeach
			</div>
			<div class="clearfix"></div>
		@endif
	@endforeach
	{{-- {{ route('calendar.search_special', ['type' => $key, 'value' => $index]) }} --}}
</div>

<script>
	$('.term-searcher-container').css('width', $('.term-searcher-container').parent().width() + 'px');

	$('.btn-special-search').on('click', function (e) {
		e.preventDefault();

		showModalCompaniesList($(this));
	});;
</script>