<script>
    function initialize()
    {
        @if (Request::session()->has('address_location'))
            geopos("{{ Request::session()->get('address_location') }}");
        @else
            geopos('Valencia, España');
        @endif

        function geopos(address)
        {
            geocoder = new google.maps.Geocoder();

            geocoder.geocode(
                {
                    'address' : address
                },
                function( results, status ) {

                    var center_position = null;
                    
                    if( status == google.maps.GeocoderStatus.OK )
                    {
                        var lat = results[0].geometry.location.lat();
                        var long = results[0].geometry.location.lng();

                        center_position = new google.maps.LatLng(lat, long);

                        mapping(center_position, lat, long);
                    }
                    else
                    {
                        console.log('Geocode was not successful for the following reason: ' + status);
                        geopos('Valencia, España');

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            method: "POST",
                            url: "{{ route('home.removeAddressSession') }}",
                            data: {address: 'Valencia, España'},
                            success: function (response) {
                                console.log(response);
                            }
                        })
                        .fail(function( jqXHR, textStatus ) {
                            console.log( jqXHR );
                            console.log( "Request failed: " + textStatus );
                        });

                    }
                }
            );
        }

        function mapping(center_position, lat, long)
        {
            // Añadimos los estilos para que no aparezcan puntos
            // de interés en el mapa
            var myStyles =[
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [
                          { visibility: "off" }
                    ]
                }
            ];

            // Parámetros del mapa
            var mapOptions = {
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.roadmap,
                scrollwheel: false,
                center: center_position,
                disableDefaultUI: true,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                styles: myStyles
            }

            // Mapa
            var map = new google.maps.Map(document.getElementById('home-map'), mapOptions);

            // Categoría
            var path = "{{ asset('wyzi/img/marker') }}";
            var dot = path + '/dot.png';
            var evento = path + '/evento.png';
            var video = path + '/video.png';
            var shopping = path + '/shopping.png';
            var shopping_offer = path + '/shopping_offer.png';
            var madical = path + '/madical.png';

            // Posición del Usuario
            var point0 = new google.maps.LatLng(lat, long);
            
            var marker0 = new google.maps.Marker({
                position: point0,
                map: map,
                icon: dot
            });

            var infowindow = new google.maps.InfoWindow();
            // Posiciones de las empresas en el mapa
            @foreach ($companies as $company)
                
                var point = new google.maps.LatLng({{ $company->lat }}, {{ $company->long }});

                var marker = new google.maps.Marker({
                    position: point,
                    map: map,
                    icon: <?=$company->hasOffer() ? 'shopping_offer' : 'shopping'?>,
                    url: "{{ route('home.company', $company->id) }}"
                });

                marker.addListener('click', function() {
                    window.location.href = "{{ route('home.company', $company->id) }}";
                });

                marker.addListener('mouseover', function () {
                    infowindow.setContent(`<div><p><strong>{{ $company->name_comercial }}</strong></p><img src="{{ asset('/img/companies/' . $company->logo) }}" width="120px" /></div>`);
                    infowindow.open(map, this);
                });

                marker.addListener('mouseout', function () {
                    infowindow.close(map, this);
                });

            @endforeach

            @foreach ($eventos as $evento)
                @if($evento->long && $evento->lat)
                    
                 //   var infowindow = new google.maps.InfoWindow();
                    
                    var point = new google.maps.LatLng({{ $evento->lat }}, {{ $evento->long }});

                    var marker = new google.maps.Marker({
                        position: point,
                        map: map,
                        icon: evento,
                        url: "{{ route('home.evento', $evento->id) }}",
                        zIndex: 9999999,
                    });

                    marker.addListener('click', function() {
                        window.location.href = "{{ route('home.evento', $evento->id) }}";
                    });

                    marker.addListener('mouseover', function () {
                        infowindow.setContent(`<div><p><strong>{{ $evento->nombre }}</strong></p><p>{!! nl2br($evento->descripcion) !!}</p><img src="{{ $evento->imagen }}" width="120px" /></div>`);
                        infowindow.open(map, this);
                    });

                    marker.addListener('mouseout', function () {
                        infowindow.close(map, this);
                    });
                @endif    

            @endforeach


        }
    }
    
    google.maps.event.addDomListener(window, 'load', initialize);   
    
</script>