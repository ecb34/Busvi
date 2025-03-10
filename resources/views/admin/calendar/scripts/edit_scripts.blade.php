    <script type="text/javascript">
	    @if (session('message'))
	        swal({
	            type: "{{ session('m_status') }}",
	            title: "{{ session('message') }}",
	            timer: 1500
	        });
	    @endif

        window.event_id = {{ $event->id }};

	    @if (isset($event->user))
        	window.crew_id = {{ $event->user->id }};
	    @endif

	    @if (isset($event->service))
        	window.service_id = {{ $event->service->id }};
	    @endif

        $('select.companies').find('option').first().attr('disabled', 'disabled');

        $('select.companies').on('change', function () {
            getCrew($(this).val());
        });
        
        $('select.crew').find('option').first().attr('disabled', 'disabled');
        
        $('select#service').find('option').first().attr('disabled', 'disabled');

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        });

        $(window).on('load', function () {
            setEventCalendar({{ $event->service_id }});

            servicesFilter();
        });

        $('.add_crew_fav').on('click', function (e) {
            e.preventDefault();

            addCrewFavourite($(this));
        });

        $(window).on('load', function () {
            servicesFilter();
        });

        eventModalEvent();

        function getCrew(id_company)
        {
            $.ajax({
                method: "GET",
                url: "{{ route('calendar.getCrew') }}",
                data: {id_company: id_company},
                success: function (response) {
                    $('form#editEvent').find('.edit_calendar_zone').append(response);

                    $('.getCalendar').on('click', function (e) {
                        e.preventDefault();

                        window.crew_id = $(this).data('crew');
                        window.service_id = $(this).data('service');

                        swal({
                            type: "info",
                            title: "Selección realizada",
                            text: "Ahora puede consultar las horas seleccionando el día que desee.",
                            timer: 1500
                        });
                        
                        setEventCalendar(service_id, crew_id);
                    });
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }

        function setEventCalendar(service_id, id)
        {
            $.ajax({
                method: "GET",
                url: "{{ route('calendar.getCalendar') }}",
                data: {service_id: window.service_id, id: window.crew_id},
                success: function (response) {
                    $('.getCalendar').on('click', function (e) {
                        e.preventDefault();

                        window.crew_id = $(this).data('crew');
                        window.service_id = $(this).data('service');

                        swal({
                            type: "info",
                            title: "Selección realizada",
                            text: "Ahora puede consultar las horas seleccionando el día que desee.",
                            timer: 1500
                        });
                        
                        setEventCalendar(service_id, crew_id);
                    });
                    
                    $('.calendar-row').remove();
                    $('form#editEvent').append(response);
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }

        function modalSetTime(date)
        {
            $('#modalEvent').find('select').empty();

            $.ajax({
                method: "POST",
                url: "{{ route('calendar.dayTime') }}",
                data: {date: date, service: window.service_id, id: window.crew_id, event: window.event_id},
                success: function (response) {
                    console.log(response)
                    for (var i = 0; i < response.length; i++)
                    {
                        var disabled = '';
                        
                        if (response[i].disabled)
                        {
                            disabled = 'disabled';
                        }
                        
                        $('#modalEvent').find('select').append('<option value="' + response[i].time + '" ' + disabled + '> ' + response[i].time + '</option>');
                    }
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }

        function eventModalEvent()
        {
            $('.add-time').on('click', function () {
                ajaxSetEvent();
            });
        }

        function ajaxSetEvent()
        {
            var service    = window.service_id
            var customer   = $('input[name="customer"]').val();
            var crew       = window.crew_id;
            var status     = $('#status').val();
            var start_date = $('#start_date').val();

            $.ajax({
                method: "POST",
                url: "{{ route('calendar.ajaxUpdateEvent', $event->id) }}",
                data: {
                    service: service,
                    customer: customer,
                    crew: crew,
                    status: status,
                    start_date: start_date
                },
                success: function (response) {
                    console.log(response)
                    window.location.replace("{{ route('calendar.index') }}");
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }

        function removeContents()
        {
            $('form#editEvent').find('.calendar-row').remove();
        }

        function servicesFilter()
        {
            $('.nav-pills').find('a').on('click', function (e) {
                e.preventDefault();

                $(this).parents('ul').find('li.active').removeClass('active');
                $(this).parents('li').addClass('active');

                fadeElements($(this).data('id'));
            });
        }

        function fadeElements(service_id)
        {
            if (service_id != 0)
            {
                $.each($('.crew-element'), function () {
                    var anchor = $(this).find('a');

                    if ($(this).find('a[data-service="' + service_id + '"]').length == 0)
                    {
                        $(this).fadeTo('fast', 0.33);

                        anchor.data('link', anchor.attr('href'));
                        anchor.removeAttr('href');
                        anchor.off('click');
                    }
                    else
                    {
                        $(this).fadeTo('fast', 1);

                        $.each(anchor, function () {
                            if ($(this).data('service') == service_id)
                            {
                                $(this).fadeTo('fast', 1);
                                $(this).attr('href', '#');
                                $(this).removeAttr('data-link');

                                serviceEvent($(this));
                            }
                            else
                            {
                                $(this).fadeTo('fast', 0.33);
                                $(this).data('link', $(this).attr('href'));
                                $(this).removeAttr('href');
                                $(this).off('click');
                            }
                        });
                    }
                });
            }
            else
            {
                $.each($('.crew-element'), function () {
                    $(this).fadeTo('fast', 1);
                });

                $.each($('.getCalendar'), function () {
                    $(this).fadeTo('fast', 1);
                    $(this).attr('href', '#');
                    $(this).removeAttr('data-link');

                    serviceEvent($(this)); 
                });
            }
        }

        function addCrewFavourite(element)
        {
            window.elementCrew = element;
            var id = element.data('id');

            $.ajax({
                method: "POST",
                url: "{{ route('crew.addCrewFavourite') }}",
                data: {id: id},
                success: function (response) {
                    console.log(response)

                    if (response)
                    {
                        if (window.elementCrew.find('i').hasClass('fa-star-o'))
                        {
                            window.elementCrew.find('i').removeClass('fa-star-o').addClass('fa-star');
                        }
                        else
                        {
                            window.elementCrew.find('i').removeClass('fa-star').addClass('fa-star-o');
                        }
                    }
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }

        function serviceEvent(element)
        {
            element.on('click', function (e) {
                e.preventDefault();

                window.crew_id = $(this).data('crew');
                window.service_id = $(this).data('service');

                swal({
                    type: "info",
                    title: "Selección realizada",
                    text: "Ahora puede consultar las horas seleccionando el día que desee.",
                    timer: 2500
                });
                
                setEventCalendar(service_id, crew_id);
            });
        }
    </script>