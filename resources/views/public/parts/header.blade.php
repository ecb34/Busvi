
        <!-- Pre Loader
        ============================================ -->
        <div class="preloader">
            <div class="loading-center">
                <div class="loading-center-absolute">
                    <div class="object object_one"></div>
                    <div class="object object_two"></div>
                    <div class="object object_three"></div>
                </div>
            </div>
        </div>
        <!-- Header
        ============================================ -->
        <div class="header static">
            <!-- Header Top -->

            <div class="header-top supercabecera">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Header Top Left -->
                        <div class="col-xs-12">
                            <h4>{{ trans('app.public.slogan') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-top social">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Header Top Right Social -->
                        <div class="header-right col-xs-12 fix">
                            <div class="header-social float-right">
                                <a href="https://www.facebook.com/Busvi-102358447903286" class="facebook" target="_blank">
                                    <i class="fa fa-facebook"></i>
                                </a>
                                <a href="https://twitter.com/BusviApp" class="twitter" target="_blank">
                                    <i class="fa fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/company/busviapp" class="twitter" target="_blank">
                                    <i class="fa fa-linkedin"></i>
                                </a>
                                <a href="https://www.youtube.com/channel/UCP-KDOVBySuG00cieeAtmqg" class="google" target="_blank">
                                    <i class="fa fa-youtube"></i>
                                </a>
                                <a href="https://www.instagram.com/busviapp/" class="instagram" target="_blank">
                                    <i class="fa fa-instagram"></i>
                                </a>
                                <!--
                                <a href="#" class="vimeo" target="_blank">
                                    <strong>B</strong>log
                                </a>
                                -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Bottom -->
            <div class="header-bottom">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="header-bottom-wrap">
                                
                                <!-- Logo -->
                                <div class="header-logo float-left">
                                    <a href="{{ route('home.index') }}">
                                        <img src="{{ asset('img/busvi_logo.jpg') }}" alt="logo" />
                                    </a>
                                </div>

                                <!-- Header Link -->
                                <?php /*
                                <div class="header-link float-right">
                                    @if (! Auth::check())
                                        @if (Request::is('company*'))
                                            <a href="#" class="button blue icon" data-toggle="modal" data-target="#loginModal">
                                                {{ trans('app.common.init_session') }} <i class="fa fa-angle-right"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="button blue icon">
                                                {{ trans('app.common.init_session') }} <i class="fa fa-angle-right"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('home.select_register_type') }}" class="button">
                                            {{ trans('app.common.register') }}
                                        </a>
                                    @else
                                        <a href="{{ route('home') }}" class="button">
                                            {{ trans('app.common.control_panel') }}
                                        </a>
                                        
                                        <a href="{{ route('exit') }}" class="button blue icon btn-logout">
                                            {{ trans('app.common.close_session') }} <i class="fa fa-angle-right"></i>
                                        </a>
                                    @endif
                                </div>
                                */ ?>

                                <!-- Main Menu -->
                                <div class="main-menu float-right hidden-sm hidden-xs">
                                    @include('layouts.menu_web')
                                </div>
                                <!-- Mobile Menu -->
                                <div class="mobile-menu hidden-lg hidden-md">
                                    @include('layouts.menu_web')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
