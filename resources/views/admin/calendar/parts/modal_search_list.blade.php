
@foreach ($companies as $company)
	<div class="row m-b-15">
		@if ($company->logo)
			<div class="col-xs-3">
				<a href="{{ route('companies.show', $company) }}" class="blog-image">
					<img src="{{ asset('img/companies/' . $company->logo) }}" alt="Busvi" class="w-100" />
				</a>
			</div>
		@endif
		<div class="col-xs-6">
			<h2 class="title m-b-0">
				<a href="{{ route('companies.show', $company) }}">
					{{ $company->name_comercial }}
				</a>
			</h2>
			<p class="mp-t-0">
				{{ $company->city }} {{ $company->province ? '(' . $company->province . ')' : '' }} 
			</p>
			<!-- Blog Meta -->
			<div class="blog-meta fix">
				<span class="author"><a href="mailto:{{ $company->admin->email }}">{{ $company->admin->email }}</a></span>
				<span class="tag"><a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
			</div>
			<a href="{{ route('companies.show', $company) }}" class="btn btn-primary btn-sm">Ver el negocio completo</a>
		</div>
		<div class="col-xs-3">
			<a href="#" class="btn btn-success btn-set-create-event m-t-35" data-company="{{ $company->id }}">
				<i class="fa fa-calendar" aria-hidden="true"></i> Pedir Cita
			</a>
		</div>
	</div>
@endforeach

<script>
    $('.btn-set-create-event').on('click', function (e) {
        e.preventDefault();

        getCrew($(this).data('company'));

        $('#modalSpecialSearch').modal('hide');
    });
</script>