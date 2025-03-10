<script>
    function initialize()
    {
        @if (Request::session()->has('address_location'))
            geopos("{{ Request::session()->get('address_location') }}");
        @else
            geopos('Valencia');
        @endif

        function geopos(address)
        {
            geocoder = new google.maps.Geocoder();

            geocoder.geocode({
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
                        alert('Geocode was not successful for the following reason: ' + status);
                        // return center_position = new google.maps.LatLng(38.902855, -77.042647);
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
                
            var point = new google.maps.LatLng({{ $company->lat }}, {{ $company->long }});

            // Parámetros del mapa
            var mapOptions = {
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.roadmap,
                scrollwheel: false,
                center: point,
                disableDefaultUI: true,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                styles: myStyles
            }
            // Mapa
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Categoría
            var path = "{{ asset('wyzi/img/marker') }}";
            var shopping = path + '/shopping<?=$company->hasOffer() ? '_offer' : ''?>.png';

            // Posiciones de las empresas en el mapa
                var infowindow = new google.maps.InfoWindow();

                var marker = new google.maps.Marker({
                    position: point,
                    map: map,
                    icon: shopping,
                    url: "{{ route('home.company', $company) }}"
                });

                marker.addListener('click', function() {
                    window.open('https://www.google.com/maps/dir/?api=1&origin=' + lat + ', ' + long + '&destination=' + this.getPosition().lat() + ', ' + this.getPosition().lng() + '&travelmode=walking');
                    // window.location.href = 'https://www.google.com/maps/dir/?api=1&origin=' + lat + ', ' + long + '=' + this.getPosition().lat() + ', ' + this.getPosition().lng() + '&travelmode=walking';
                    count_event(<?=$company->id?>, 'map');
                });

                marker.addListener('mouseover', function () {
                    infowindow.setContent('<div><p><strong>{{ $company->name_comercial }}</strong></p><img src="{{ asset('/img/companies/' . $company->logo) }}" width="120px" /></div>');
                    infowindow.open(map, this);
                    // this.setIcon('http://www.christielakekids.com/_images/map_pins/events/canoe-for-kids.png');
                });

                marker.addListener('mouseout', function () {
                    infowindow.close(map, this);
                    // this.setIcon(shopping);
                });
        }
    }
    
    google.maps.event.addDomListener(window, 'load', initialize);   
</script>