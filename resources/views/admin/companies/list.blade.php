@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>Negocios</h3>
    </section>
    <div class="content">
		<div class="row">
			<div class="blogs col-xs-12">
				<!-- Single Blog -->
				@foreach ($companies as $company)
					<div class="row m-b-15 list-item">
						<div class="col-xs-9">
							<div class="row">
								@if ($company->logo)
									<div class="col-xs-3">
										<a href="{{ route('companies.show', $company) }}" class="blog-image">
											<img src="{{ asset('img/companies/' . $company->logo) }}" alt="Busvi" class="w-100" />
										</a>
									</div>
								@endif
								<div class="col-xs-9">
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
									<a href="{{ route('companies.show', $company) }}" class="read-more">Ver el negocio completo</a>
								</div>
							</div>
						</div>
						<div class="col-xs-3 text-center">
							<h3>
								@if (! $company->km)
									No es posible determinar distancia.
								@else
									<small>Se encuentra a</small>
									<br>
									{{ $company->km }} km
								@endif
							</h3>
						</div>
					</div>
				@endforeach
				<!-- Blog Pagination -->
				{{ $companies->links() }}
				{{-- <div class="blog-pagination fix">
					<a href="#" class="prev-page float-left"><i class="fa fa-angle-left"></i>Previous Page</a>
					<a href="#" class="next-page float-right">Next Page<i class="fa fa-angle-right"></i></a>
				</div> --}}
			</div>
		</div>
    </div>
@endsection