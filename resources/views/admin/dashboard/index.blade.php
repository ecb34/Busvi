@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>Dashboard</h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('vendor.flash.message')

        <div class="clearfix"></div>

        @include('admin.dashboard.parts.header_icons')

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header">
                        <h4><i class="fa fa-calendar-check-o"></i> {{ trans('app.admin.dashboard.the_next_events') }}</h4>
                    </div>
                    <div class="box-body">
                        {!! $datatable_next_events['datatable'] !!}
                    </div>
                </div>
            </div>
        </div>

        <?php if(\Auth::user()->role == 'user'){ ?>
        @include('admin.reservas.listado_reservas_home')
        <?php } ?>

        <div class="row">
            <div class="col-xs-8">
                <div class="box box-success">
                    <div class="box-header">
                        <h4><i class="fa fa-calendar"></i> {{ trans('app.admin.dashboard.all_events') }}</h4>
                    </div>
                    <div class="box-body">
                        {!! $datatable_events['datatable'] !!}
                    </div>
                </div>
            </div>

            @if (Auth::user()->role != 'admin' && Auth::user()->role != 'crew')
                <div class="col-xs-4">
                    <div class="box box-danger">
                        <div class="box-header">
                            <h4><i class="fa fa-heart"></i> {{ trans('app.admin.dashboard.companies_favourites') }}</h4>
                        </div>
                        <div class="box-body">
                            {!! $datatable_companies_fav['datatable'] !!}
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-xs-4">
                <div class="box box-danger">
                    <div class="box-header">
                        <h4><i class="fa fa-heart"></i> {{ trans('app.admin.dashboard.crews_favourites') }}</h4>
                    </div>
                    <div class="box-body">
                        {!! $datatable_crew_fav['datatable'] !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="box box-solid bg-teal-gradient">
                    <div class="box-body">
                        <div class="chart" id="line-chart"></div>
                    </div>
                </div>
            </div>
                
            <div class="col-xs-12 col-sm-4">
                <div class="box box-solid">
                    <div class="box-header">
                        Algo que medir
                    </div>
                    <div class="box-body text-center">
                        <input type="text" class="knob" data-readonly="true" value="20" data-fgcolor="#39CCCC" readonly="readonly">
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection

@section('scripts')
    {!! $datatable_next_events['script'] !!}
    {!! $datatable_events['script'] !!}
    {!! $datatable_companies_fav['script'] !!}
    {!! $datatable_crew_fav['script'] !!}

    <script>
        $(function () {
            var line = new Morris.Line({
                element          : 'line-chart',
                resize           : true,
                data             : [
                  { y: '2011 Q1', item1: 2666 },
                  { y: '2011 Q2', item1: 2778 },
                  { y: '2011 Q3', item1: 4912 },
                  { y: '2011 Q4', item1: 3767 },
                  { y: '2012 Q1', item1: 6810 },
                  { y: '2012 Q2', item1: 5670 },
                  { y: '2012 Q3', item1: 4820 },
                  { y: '2012 Q4', item1: 15073 },
                  { y: '2013 Q1', item1: 10687 },
                  { y: '2013 Q2', item1: 8432 }
                ],
                xkey             : 'y',
                ykeys            : ['item1'],
                labels           : ['Item 1'],
                lineColors       : ['#efefef'],
                lineWidth        : 2,
                hideHover        : 'auto',
                gridTextColor    : '#fff',
                gridStrokeWidth  : 0.4,
                pointSize        : 4,
                pointStrokeColors: ['#efefef'],
                gridLineColor    : '#efefef',
                gridTextFamily   : 'Open Sans',
                gridTextSize     : 10
            });

            $('.box ul.nav a').on('shown.bs.tab', function () {
                line.redraw();
            });
        });
    </script>
@append