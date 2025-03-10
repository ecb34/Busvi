<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

        <?php
            $title = env('APP_TITLE', 'Busvi');
            if(isset($meta_title)){
                $title = $meta_title . ' - ' . $title;
            }
        ?>

        <title>{{ $title }}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="_token" content="{!! csrf_token() !!}"/>

        <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-timepicker/css/timepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/autocomplete/jquery.auto-complete.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/AdminLTE.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/skins/_all-skins.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/morris.js/morris.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css"/>
        <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/iCheck/flat/blue.css')}}">
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap-tags-input/bootstrap-tagsinput.css') }}">
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/custom_admin.css') }}">

        @yield('css')

    </head>
    <body class="skin-yellow-light sidebar-mini" id="adminlte">
        <div class="wrapper">
            <!-- Main Header -->
            <header class="main-header">

                <!-- Logo -->
                <a href="<?=\URL::route('home')?>" class="logo">
                    <img src="<?=asset('img/busvi_logo_solo.jpg')?>">
                </a>

                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <div class="m-t-35 m-l-15 pull-left">
                        <a href="{{ route('home.index') }}" class="btn btn-primary">
                            Ir a la web
                        </a>
                    </div>

                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    @if (Auth::check() && Auth::user()->img)
                                        @if (Auth::user()->role == 'user')
                                            <img src="{{ asset('/img/user/' . Auth::user()->img) }}" class="img-circle" alt="{{ Auth::user()->name }}" width="45px" />
                                        @else
                                            <img src="{{ asset('/img/crew/' . Auth::user()->img) }}" class="img-circle" alt="{{ Auth::user()->name }}" width="45px" />
                                        @endif
                                    @else
                                        <img src="{{ asset('/img/user.jpg') }}" class="img-circle" width="45px"  alt="User Image"/>
                                    @endif
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span class="hidden-xs">{!! Auth::user()->name !!}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        @if (Auth::check() && Auth::user()->img)
                                            <img src="{{ asset('/img/crew/' . Auth::user()->img) }}" class="img-circle" alt="{{ Auth::user()->name }}" width="45px" />
                                        @else
                                            <img src="{{ asset('/img/user.jpg') }}" class="img-circle" width="45px"  alt="User Image"/>
                                        @endif
                                        
                                        <p>
                                            {!! Auth::user()->name !!}
                                            <small>{{ trans('app.admin.layout.member_from') }} {!! Auth::user()->created_at->format('M. Y') !!}</small>
                                        </p>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <?php $route = route('crew.edit', Auth::user()->id); ?>
                                            @if (Auth::user()->role == 'user')
                                                <?php $route = route('users.edit', Auth::user()->id); ?>
                                            @endif
                                            <a href="{{ $route }}" class="btn btn-default btn-flat">{{ trans('app.admin.layout.profile') }}</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="{!! url('/logout') !!}" class="btn btn-default btn-flat"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                {{ trans('app.common.exit') }}
                                            </a>
                                            <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                                  style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <!-- Left side column. contains the logo and sidebar -->
            @include('layouts.sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                @yield('content')
            </div>

            <!-- Main Footer -->
            <footer class="main-footer" style="max-height: 100px;text-align: center">
                <strong>Copyright © <?=date('Y')?> <a href="http://www.busvi.es">Busvi</a>.</strong> All rights reserved.
            </footer>
        </div>

        @yield('modals')

        <!-- jQuery 3.1.1 -->
        <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('bower_components/morris.js/morris.js') }}"></script>
        <script src="{{ asset('bower_components/raphael/raphael.min.js') }}"></script>
        <script src="{{ asset('bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('bower_components/autocomplete/jquery.auto-complete.js') }}"></script>
        <script src="{{ asset('bower_components/moment/min/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('bower_components/iCheck/icheck.min.js') }}"></script>
        <script src="{{ asset('bower_components/fastclick/fastclick.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

        <!-- AdminLTE App -->
        <script src="{{ asset('js/app.min.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                /* jQueryKnob */
                $('.knob').knob();

                // $('input').iCheck({
                //     checkboxClass: 'icheckbox_flat-blue',
                //     radioClass: 'iradio_flat-blue'
                // });

                $('.select2').select2();
            });
        </script>

        <!-- Datatable Scripts -->
        <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>

        <!-- SweetAlert2 -->
        <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

        <!-- FullCalendar -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/es.js"></script>

        <!-- CKEditor -->
        {{-- <script src="https://cdn.ckeditor.com/ckeditor5/1.0.0-beta.3/classic/ckeditor.js"></script> --}}
        {{-- <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script> --}}
        <script src="{{ asset('vendor/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
        <script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>

        <!-- Tags Input -->
        <script src="{{ asset('plugins/bootstrap-tags-input/bootstrap-tagsinput.min.js') }}"></script>

        <!-- Bootstrap Tootle -->
        <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

        <script>
            $(document).ready(function(){

                @if (session('message'))
                    swal({
                        type: "{{ session('m_status') }}",
                        title: "{{ session('message') }}",
                        timer: 2500
                    });
                @endif

            });
        </script>

        @yield('scripts')
    </body>
</html>
