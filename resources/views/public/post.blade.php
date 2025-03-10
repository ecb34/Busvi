@extends('layouts.web')

@section('content')
    <!-- Breadcrumbs
    ============================================ -->
    <div class="page-title-social">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title float-left">
                    	<h2>{{ $post->title }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<!-- Single Blog Page Area
	============================================ -->
	<div class="blog-page-area margin-bottom-50 post">
		<div class="container">
			<div class="row">
				<div class="blogs col-xs-12">
					<div class="sin-blog">
						<div class="content">
							{!! $post->body !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
