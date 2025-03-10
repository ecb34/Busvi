<aside class="main-sidebar" id="sidebar-wrapper">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                @if (Auth::check() && Auth::user()->img)
                    @if (Auth::user()->role == 'user')
                        <img src="{{ asset('/img/user/' . Auth::user()->img) }}" class="img-circle" alt="{{ Auth::user()->name }}" width="45px" />
                    @else
                        <img src="{{ asset('/img/crew/' . Auth::user()->img) }}" class="img-circle" alt="{{ Auth::user()->name }}" width="45px" />
                    @endif
                @else
                    <img src="{{ asset('/img/user.jpg') }}" class="img-circle" width="45px"  alt="User Image"/>
                @endif
            </div>
            <div class="pull-left info">
                @if (Auth::guest())
                    <p>{{env('APP_TITLE','Busvi')}}</p>
                @else
                    <p>{{ Auth::user()->name}}</p>
                @endif
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('app.common.online') }}</a>
            </div>
        </div>

        <!-- search form (Optional)
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Buscar..."/>
          <span class="input-group-btn">
            <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
            </div>
        </form>-->
        <!-- Sidebar Menu -->

        <ul class="sidebar-menu">
            @include('layouts.menu')
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
