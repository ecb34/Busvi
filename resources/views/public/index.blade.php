@extends('layouts.web')

@section('content')

        <?php /* if(!is_null($slider_inicio) && $slider_inicio->media->count() > 0){ ?>
        <div class="owl-carousel owl-theme slider-inicio">
            <?php foreach($slider_inicio->media as $slide){ ?>
                <?php if(isset($slide->custom_properties['link']) && $slide->custom_properties['link'] != ''){ ?><a href="<?=$slide->custom_properties['link']?>"><?php } ?>
                <img src="<?=$slide->getFullUrl()?>" <?php if(isset($slide->custom_properties['title']) && $slide->custom_properties['title'] != ''){ ?>title="<?=$slide->custom_properties['title']?>"<?php } ?>>
                <?php if(isset($slide->custom_properties['link']) && $slide->custom_properties['link'] != ''){ ?></a><?php } ?>
            <?php } ?>
        </div>
        <?php } */ ?>

        <?php
            $media = null;
            // if(!is_null($slider_inicio) && $slider_inicio->media->count() > 0){
            //     $media = $slider_inicio->media[rand(0, $slider_inicio->media->count() - 1)];
            // }
        ?>
        <div style="margin-bottom: 10px; overflow: hidden; <?php if(!is_null($media)){ ?>background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('<?=asset($media->getFullUrl())?>') center center; background-size: cover;<?php } ?>">
            <div class="container">
                <video width="100%" autoplay muted controls style="margin-bottom: -7px;">
                    <source src="<?=asset('busvi.mp4')?>" type="video/mp4">
                </video> 
            </div>
        </div>

        <div class="container plano-inicio">
            <!-- Map
            ============================================ -->
            <div class="map-container home-map-container">
                <div id="home-map"></div>
                <!-- Location Search -->
                <div class="location-search-float">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                                <div class="location-search">
                                    <h2>{{ trans('app.public.your_location') }}</h2>
                                    {!! Form::open(['route' => ['home.getLocation'], 'method' => 'POST']) !!}
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                                                {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Calle, Dirección (ej: Calle Colón, Valencia)']) !!}
                                                <button type="submit" class="button blue icon pull-right">
                                                    <i class="fa fa-search"></i> {{ trans('app.public.change') }}
                                                </button>
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                    <hr>
                                {{--   <div class="row m-t-15">
                                        <div class="col-xs-12 col-sm-5 col-sm-offset-1">
                                            {!! Form::select('sector', $sectors, null, ['class' => 'form-control companies', 'placeholder' => 'Escoje sector']) !!}
                                        </div>
                                        <div class="col-xs-12 col-sm-5">
                                            <a href="#" class="btn-company text-center">
                                                <i class="fa fa-list"></i> Mostrar Listado Negocios
                                            </a>
                                        </div>
                                    </div>--}}
                                    <div class="row m-t-15">
                                        {!! Form::open(['route' => 'home.tags', 'method' => 'POST']) !!}
                                            <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                                                {!! Form::text('tags', null, ['class' => 'form-control companies', 'placeholder' => 'Añade términos de búsqueda']) !!}
                                                <button type="submit" href="#" class="button orange icon pull-right">
                                                    <i class="fa fa-list"></i> {{ trans('app.public.companies_list') }}
                                                </button>
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <div class="row m-t-15">
                                        {!! Form::open(['route' => 'home.eventtags', 'method' => 'POST']) !!}
                                            <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                                                {!! Form::text('eventtags', null, ['class' => 'form-control events', 'placeholder' => 'Añade términos de búsqueda']) !!}
                                                <button type="submit" href="#" class="button orange icon pull-right">
                                                    <i class="fa fa-list"></i> {{ trans('app.public.events_list') }}
                                                </button>
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                        {{-- <div class="input-kayword"><input type="text" placeholder="search keywords" /></div>
                                        <div class="input-location"><input type="text" placeholder="all location" /></div>
                                        <div class="input-range orange">
                                            <p>Radius:  <span></span></p>
                                            <input type="range" value="70" min="0" max="180" />
                                        </div>
                                        <div class="input-submit">
                                            <button><i class="fa fa-search"></i> search</button>
                                        </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!is_null($slider_servicios) && $slider_servicios->media->count() > 0){ ?>
        <div class="container">
            <h2 class="servicios">Servicios</h2>
            <div class="owl-carousel owl-theme slider-inicio">
                <?php foreach($slider_servicios->media as $slide){ ?>
                <div class="item">
                    <?php if(isset($slide->custom_properties['link']) && $slide->custom_properties['link'] != ''){ ?><a href="<?=$slide->custom_properties['link']?>"><?php } ?>
                    <img src="<?=$slide->getFullUrl()?>" <?php if(isset($slide->custom_properties['title']) && $slide->custom_properties['title'] != ''){ ?>title="<?=$slide->custom_properties['title']?>"<?php } ?>>
                    <?php if(isset($slide->custom_properties['link']) && $slide->custom_properties['link'] != ''){ ?></a><?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div id="contadores">
            <div class="container">
                <div class="row">
                    
                    <div class=" col-sm-2 col-xs-6 text-center">
                        <h3 class="m-b-15">Negocios</h3>
                        <h2 class="counter"><span class="count" data-number="{{ $companies->count() }}">0</span></h2>
                    </div>

                    <div class="col-sm-2 col-xs-6 text-center">
                        <h3 class="m-b-15">Profesionales</h3>
                        <h2 class="counter"><span class="count" data-number="{{ $crews }}">0</span></h2>
                    </div>

                    <div class="col-sm-2 col-xs-6 text-center">
                        <h3 class="m-b-15">Usuarios</h3>
                        <h2 class="counter"><span class="count" data-number="{{ $customers }}">0</span></h2>
                    </div>

                    <div class="col-sm-2 col-xs-6 text-center">
                        <h3 class="m-b-15">Citas</h3>
                        <h2 class="counter"><span class="count" data-number="{{ $events }}">0</span></h2>
                    </div>

                    <div class="col-sm-2 col-xs-6 text-center">
                        <h3 class="m-b-15">Reservas</h3>
                        <h2 class="counter"><span class="count" data-number="{{ $reservas }}">0</span></h2>
                    </div>

                    <div class="col-sm-2 col-xs-6 text-center">
                        <h3 class="m-b-15">Eventos</h3>
                        <h2 class="counter"><span class="count" data-number="{{ $n_eventos }}">0</span></h2>
                    </div>
                    

                </div>
            </div>
        </div>

        <!-- Recently Added
        ============================================ -->
        <div class="recently-added-area margin-bottom-50">
            <div class="container">
                <div class="row">
                    <!-- Section Title -->
                    <div class="section-title col-xs-12 margin-bottom-50">
                        <h1>{{ trans('app.common.last_companies') }}</h1>
                    </div>
                    <div class="col-xs-12">
                        <!-- Recently Added Slider -->
                        <div class="recently-added-slider">
                            @foreach ($recently as $company)
                                <div class="sin-added-item">
                                    <a href="{{ route('home.company', $company) }}" class="image">
                                        <img src="{{ asset('img/companies/') . '/' . $company->logo }}" alt="" />
                                    </a>
                                    <div class="text fix">
                                        <h2>
                                            <a href="{{ route('home.company', $company) }}">
                                                {{ $company->name_comercial }}
                                            </a>
                                        </h2>
                                        <p>{{ str_limit($company->description, 120) }}</p>
                                        <p class="">
                                          <a class="button orange icon pull-right" href="{{ route('home.company', $company) }}"><i class="fa fa-chevron-right"></i> {{ trans('app.public.read_more') }}</a>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4y_g7YyNlF_V2N5PFn4qzPa85Z0XswYw&libraries=places"></script>
    @include('public.scripts.index_home_map')

    <script>

        var contador_ejecutado = false;

        $(function () {

            $('[data-toggle="tooltip"]').tooltip();

            $(window).scroll(function() {

              var docViewTop = $(window).scrollTop();
              var docViewBottom = docViewTop + $(window).height();

              var elemTop = $('#contadores').offset().top;
              if(!contador_ejecutado && docViewBottom > elemTop){
                ejecutar_contador();
              }

            });
            $(window).trigger('scroll');

            $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 10,
                nav: false,
                items: 1,
                dots: false,
                autoplay: true,
                autoplayHoverPause: true,
                autoplaySpeed: 1000,
            })

        });

        function ejecutar_contador(){
          if(!contador_ejecutado){
            contador_ejecutado = true;
            $.each($('.count'), function () {
              $(this).animateNumber({
                number: $(this).data('number')
              }, 3000);
            });
          }
        }

        $('.btn-company').on('click', function (e) {
            e.preventDefault();

            var sector = $('select[name="sector"]').val();
            var url = "{{ route('home.list') }}/" + sector;

            window.location.replace(url);
        });

    </script>
@endsection
