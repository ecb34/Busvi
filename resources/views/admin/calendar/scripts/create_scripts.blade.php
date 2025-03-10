    <script type="text/javascript">        
        /*$('.calendar').datetimepicker({
            locale: 'es',
            format: 'LT'
        });*/

        window.crew_id = null;
        window.service_id = null;

        @if (Auth::user()->role != 'user')
            $('input[name="term"]').prop('disabled', true);
            $('input[name="search-company"]').prop('disabled', true);
        @endif

        $('input[name="search-customer"]').autoComplete({
            minChars: 1,
            source: function(term, suggest) {
                term = term.toLowerCase();

                var choices = [
                    @foreach ($customers as $index => $value)
                        ["{{ $value }}", "{{ $index }}"],
                    @endforeach
                ];
                var suggestions = [];

                for (i = 0;i < choices.length; i++)
                {
                    if (~(choices[i][0] + ' ' + choices[i][1]).toLowerCase().indexOf(term))
                    {
                        suggestions.push(choices[i]);
                    }
                }

                suggest(suggestions);
            },
            renderItem: function (item, search) {
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');

                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

                return '<div class="autocomplete-suggestion" data-name="'+item[0]+'" data-id="'+item[1]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
            },
            onSelect: function(e, term, item) {
                $('input[name="search-customer"]').val(item.data('name'));
                $('input[name="customer"]').val(item.data('id'));

                $('input[name="term"]').prop('disabled', false);
                $('input[name="search-company"]').prop('disabled', false);
            }
        });

        $('input[name="customer"]').on('change', function () {
            if ($(this).val() == null)
            {
                $('input[name="term"]').prop('disabled', true);
                $('input[name="search-company"]').prop('disabled', true);
            }
        });

        $('input[name="term"]').on('keyup', function () {
            ajaxGetByTerm($(this).val());
        });

        $('input[name="term"]').on('change', function () {
            if ($(this).val() == '')
            {
                $('.term-searcher-container').remove();
            }
        });

        $(document).mouseup(function(e) 
        {
            var container = $('.term-searcher-container');

            // Si el objetivo del click no es el contenedor ni un descendiente del contenedor.
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                container.remove();
            }
        });

        $('#modalSpecialSearch').on('hidden.bs.modal', function (e) {
            $(this).find('.modal-body').empty();
        });

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        });

        $(window).on('load', function () {
            servicesFilter();
        });

        eventModalEvent();

        setAutocompleteCompany();

        @if ($company_selected)
            getCrew($('input[name="company"]').val());
        @endif

        function getCrew(id_company)
        {
            $.ajax({
                method: "GET",
                url: "{{ route('calendar.getCrew') }}",
                data: {id_company: id_company},
                success: function (response) {
                    $('form#createEvent').append(response);
                    
                    $('.add_crew_fav').on('click', function (e) {
                        e.preventDefault();

                        addCrewFavourite($(this));
                    });

                    serviceEvent($('.getCalendar'));

                    servicesFilter();
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
                    $('.calendar-row').remove();
                    $('form#createEvent').append(response);

                    setAutocompleteCompany();
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
            
            var service = $('#service').val();
            var id = $('select[name="crew"]').val();

            $.ajax({
                method: "POST",
                url: "{{ route('calendar.dayTime') }}",
                data: {date: date, service: window.service_id, id: window.crew_id},
                success: function (response) {
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
                url: "{{ route('calendar.store') }}",
                data: {
                    service: service,
                    customer: customer,
                    crew: crew,
                    status: status,
                    start_date: start_date
                },
                success: function (response) {
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
            $('form#createEvent').find('.crew').remove();
            $('form#createEvent').find('.calendar-row').remove();
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

        function setAutocompleteCompany()
        {
            $('input[name="search-company"]').autoComplete({
                minChars: 1,
                source: function(term, suggest) {
                    term = term.toLowerCase();

                    var choices = [
                        @foreach ($companies as $index => $value)
                            ["{{ $value }}", "{{ $index }}"],
                        @endforeach
                    ];
                    var suggestions = [];

                    for (i = 0;i < choices.length; i++)
                    {
                        if (~(choices[i][0] + ' ' + choices[i][1]).toLowerCase().indexOf(term))
                        {
                            suggestions.push(choices[i]);
                        }
                    }

                    suggest(suggestions);
                },
                renderItem: function (item, search) {
                    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');

                    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

                    return '<div class="autocomplete-suggestion" data-name="'+item[0]+'" data-id="'+item[1]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
                },
                onSelect: function(e, term, item) {
                    $('input[name="search-company"]').val(item.data('name'));
                    $('input[name="company"]').val(item.data('id'));

                    removeContents();
                    getCrew(item.data('id'));
                }
            });
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

        function ajaxGetByTerm(value)
        {
            $.ajax({
                method: "POST",
                url: "{{ route('calendar.termSearch') }}",
                data: {term: value},
                beforeSend: function () {
                    $('.term-searcher-container').remove();
                },
                success: function (response) {
                    console.log(response)
                    $('.term-search-wrapper').append(response);
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }

        function showModalCompaniesList(element)
        {
            $.ajax({
                method: "POST",
                url: "{{ route('calendar.search_special') }}",
                data: {
                    type: element.data('type'),
                    value: element.data('value')
                },
                success: function (response) {
                    removeContents();
                    $('#modalSpecialSearch').find('.modal-body').append(response);
                    $('#modalSpecialSearch').modal('show');
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.log( jqXHR );
                console.log( "Request failed: " + textStatus );
            });
        }
    </script>
