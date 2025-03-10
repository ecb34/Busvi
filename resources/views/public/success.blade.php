@extends('layouts.web')

@section('content')
    <!-- Single Blog Page Area
	============================================ -->
	<div class="blog-page-area margin-bottom-100">
		<div class="container">
			<div class="row">
				<div class="blogs col-xs-12">
					<!-- Single Blog -->
					<div class="sin-blog">
						<div class="content">
							<div class="row text-center">
								<div class="col-xs-12 m-b-15">
									<h1>{{ trans('app.public.transaction_success') }}</h1>
								</div>
								<div class="col-xs-6 m-t-35">
									<a href="{{ route('home.index') }}" class="btn btn-primary">{{ trans('app.common.back_home') }}</a>
								</div>
								<div class="col-xs-6 m-t-35">
									<a href="{{ route('home') }}" class="btn btn-primary">{{ trans('app.common.back_control_panel') }}</a>
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
	<script type="text/javascript">
	    @if (session('message'))
	        swal({
	            type: "{{ session('m_status') }}",
	            title: "{{ session('message') }}",
	            timer: 1500
	        });
	    @endif
	</script>
@endsection
