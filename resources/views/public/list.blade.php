@extends('layouts.web')

@section('content')
	<!-- Page Title & Social Area
	============================================ -->
	<div class="page-title-social">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-title float-left"><h2>{{ trans('app.common.companies_list') }}</h2></div>
				</div>
			</div>
		</div>
	</div>
	<!-- Blog Page Area
	============================================ -->
	<div class="blog-page-area margin-bottom-100 margin-top-50">
		<div class="container">
			<div class="row">
				<div class="blogs col-xs-12">
					<!-- Single Blog -->
					@if ($companies)
						@foreach ($companies as $company)
							<div class="sin-blog">
								<!-- Blog Content -->
								<div class="content">
									<div class="row">
										<div class="col-xs-9">
											<div class="row">
												<div class="col-xs-3">
													<!-- Blog Image -->
													<a href="{{ route('home.company', $company) }}" class="blog-image">
														<img src="{{ asset('img/companies/' . $company->logo) }}" alt="" class="w-100" />
													</a>
												</div>
												<div class="col-xs-9">
													<h2 class="title m-b-0">
														<a href="{{ route('home.company', $company) }}">
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
													<a href="{{ route('home.company', $company) }}" class="read-more">{{ trans('app.public.show_company') }}</a>
												</div>
											</div>
										</div>
										<div class="col-xs-3 text-center">
											<h3>
												@if (! $company->km)
													{{ trans('app.public.not_distance') }}
												@else
													<small>{{ trans('app.public.is_at') }}</small>
													{{ $company->km }} km
												@endif
											</h3>
										</div>
									</div>
								</div>
							</div>
						@endforeach

						<!-- Blog Pagination -->
						{{ $companies->links() }}
					@else
						<h3>No se han encontrado negocios con esos términos. <a href="{{ route('home.index') }}" title="Vuelva al inicio" class="text-primary">Vuelva al inicio</a> e inténtelo de nuevo.</h3>
					@endif
					
					{{-- <div class="blog-pagination fix">
						<a href="#" class="prev-page float-left"><i class="fa fa-angle-left"></i>Previous Page</a>
						<a href="#" class="next-page float-right">Next Page<i class="fa fa-angle-right"></i></a>
					</div> --}}
				</div>
			</div>
		</div>
	</div>
@endsection