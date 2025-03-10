@extends('layouts.web')

@section('content')
    <!-- Breadcrumbs
    ============================================ -->
    <div class="page-title-social">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title float-left">
                    	<h2>{{ trans('app.common.contact') }}</h2>
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

              <div class="row">
                <div class="col-sm-8 col-xs-12">

                  {!! Form::open(['route' => 'home.sendContactForm', 'method' => 'POST']) !!}

                    <div class="input-box">
                      <label for="name">{{ trans('app.common.name') }}:</label>
                      <input class="form-control" required="required" name="name" type="text" id="name">
                    </div>

                    <div class="input-two space-80">
                      <div class="input-box">
                        <label for="phone">{{ trans('app.common.phone') }}:</label>
                        <input class="form-control" required="required" name="phone" type="text" id="phone">
                      </div>
                      <div class="input-box">
                        <label for="email">{{ trans('app.common.email') }}:</label>
                        <input class="form-control" required="required" name="email" type="email" id="email">
                      </div>
                    </div>

                    <div class="input-box">
                      <label for="mensaje">{{ trans('app.common.message') }}:</label>
                      <textarea class="form-control" required="required" name="mensaje" id="mensaje"></textarea>
                    </div>

                    <div class="form-group">
                      <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha_key') }}"></div>
                      <button class="button orange icon big pull-right m-t-15" type="submit">
                          {{ trans('app.common.send') }} <i class="fa fa-angle-right"></i>
                      </button>
                      <div class="clearfix"></div>
                    </div>

                  {!! Form::close() !!}

                </div>
                <div class="col-sm-4 col-xs-12">
                  {!! $post->body !!}
                </div>
              </div>


						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection
