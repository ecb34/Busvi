@extends('layouts.web')

@section('content')
<!-- Business Tab Area
============================================ -->
<div class="login-page centrado">
	<div class="container ">
		<div class="row">
			<!-- Title & Search -->
			<div class="section-title text-center col-xs-12 margin-bottom-50">
				<h1>{{ trans('app.public.select_type_register') }}</h1>
			</div>
			<!-- Contact Form -->
			<div class="register-form text-center col-xs-6">
				<a href="{{ route('home.register') }}" class="button blue icon big">
					<i class="fa fa-user" aria-hidden="true"></i> {{ trans('app.public.user_register_short') }}
				</a>
			</div>
			<div class="register-form text-center col-xs-6">
				<a href="{{ route('home.register_company') }}" class="button blue icon big">
					<i class="fa fa-building" aria-hidden="true"></i> {{ trans('app.public.company_register_short') }}
				</a>
			</div>
		</div>
	</div>
</div>
@endsection
