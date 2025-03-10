<section class="panel panel-default" id="inicio">
    <div class="row m-l-none m-r-none m-b-none bg-light b-b lter">
        <div class="col-sm-6 col-md-3 padder-v b-r b-light">
            <span class="fa-stack fa-2x pull-left m-r-sm m-10">
                <i class="fa fa-circle fa-stack-2x text-info"></i>
                <i class="fa fa-calendar-check-o fa-stack-1x text-white icono"></i>
            </span>
            <h3>
                <a class="text-info" href="{{ route('calendar.nextEvents', Auth::user()->id) }}">
                    <span class="h3 block m-t-xs"><strong>{{ $next_events->count() }}</strong></span>
                    <small class="text-muted text-uc">{{ trans('app.admin.dashboard.next_events') }}</small>
                </a>
            </h3>
        </div>
        <div class="col-sm-6 col-md-3 padder-v b-r b-light">
            <span class="fa-stack fa-2x pull-left m-r-sm m-10">
                <i class="fa fa-circle fa-stack-2x text-success"></i>
                <i class="fa fa-calendar fa-stack-1x text-white icono"></i>
            </span>
            <h3>
                <a class="text-success" href="{{ route('calendar.index') }}">
                    <span class="h3 block m-t-xs"><strong>{{ $events->count() }}</strong></span>
                    <small class="text-muted text-uc">{{ trans('app.admin.dashboard.total_events') }}</small>
                </a>
            </h3>
        </div>
        <div class="col-sm-6 col-md-3 padder-v b-r b-light">
            <span class="fa-stack fa-2x pull-left m-r-sm m-10">
                <i class="fa fa-circle fa-stack-2x text-warning"></i>
                <i class="fa fa-heart fa-stack-1x text-white icono"></i>
            </span>
            <h3>
                <a class="text-warning" href="#">
                    <span class="h3 block m-t-xs"><strong>{{ $companies_fav->count() }}</strong></span>
                    <small class="text-muted text-uc">{{ (Auth::user()->role == 'admin') ? '' : 'Negocios ' }}{{ trans('app.common.favourites') }}</small>
                </a>
            </h3>
        </div>
        <div class="col-sm-6 col-md-3 padder-v b-r b-light">
            <span class="fa-stack fa-2x pull-left m-r-sm m-10">
                <i class="fa fa-circle fa-stack-2x text-danger"></i>
                <i class="fa fa-heart-o fa-stack-1x text-white icono"></i>
            </span>
            <h3>
                <a class="text-danger" href="#">
                    <span class="h3 block m-t-xs"><strong>{{ $crew_fav->count() }}</strong></span>
                    <small class="text-muted text-uc">{{ trans('app.admin.dashboard.crews_favourites') }}</small>
                </a>
            </h3>
        </div>
    </div>
</section>