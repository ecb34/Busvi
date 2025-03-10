@extends('layouts.web')

@section('content')
<!-- Business Tab Area
============================================ -->
<div class="login-page margin-100">
    <div class="container">
        <div class="row">
            <!-- Title & Search -->
            <div class="section-title text-center col-xs-12 margin-bottom-50">
                <h1>{{ trans('app.public.access_disabled') }}</h1>
            </div>
            <!-- Contact Form -->
            <div class="register-form text-center col-xs-12 col-sm-12">
                <p>
                    {{ trans('app.public.access_disabled_txt_1') }}
                </p>
                <p>
                    {{ trans('app.public.access_disabled_txt_2') }}
                </p>
                <p>
                    {{ trans('app.public.access_disabled_txt_3') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection