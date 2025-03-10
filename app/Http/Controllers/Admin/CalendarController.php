<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Event;
use App\Favourite;
use App\Company;
use App\CompanyTag;
use App\User;
use App\Sector;
use App\Service;
use App\ServicesUser;
use App\EventUserBlocked;

use Calendar;
use Carbon\Carbon;
use Auth;

use App\Jobs\PushCreateEventJob;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	if (Auth::user()->role == 'user')
    	{
    		return redirect()->route('users.edit', ['id' => Auth::user()->id]);
    	}

        $datatable_events = $this->datatableEvents();

        $datatable_companies = NULL;

        if (Auth::user()->role == 'user')
        {
            $datatable_companies = $this->datatableCompanies(Auth::user()->id);
        }

        return view('admin.calendar.index', [
                                                'datatable' => $datatable_events['datatable'],
                                                'script' => $datatable_events['script'],
                                                'search' => $datatable_events['search'],
                                                'datatable_companies' => $datatable_companies
                                            ]);
    }

    private function datatableEvents()
    {
        $events = new Event();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
        {
            $events = $events->all();
        }
        elseif (Auth::user()->role == 'user')
        {
            $events = $events->where('customer_id', Auth::user()->id)->get();
        }
        else
        {
            $events = Auth::user()->company->events;
        }

        $array_datas = ['service_id', 'customer_id', 'user_id', 'user_id', 'start_date', 'end_date', 'id'];
        $array_titles = ['Servicio', 'Usuario', 'Profesional', 'Negocio', 'Inicio', 'Fin', ''];

        $datatable = $dt->datatable(
                                    'datatable_events', $events, $array_datas, 'edit', 'calendar', $buscadores, 'admin.calendar.datatable.datatable', $array_titles
                                );

        $script = $dt->script('datatable_events', $buscadores);

        return ['datatable' => $datatable, 'script' => $script, 'search' => $buscadores];
    }

    private function datatableCompanies($id = null)
    {
        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        if (! $id)
        {
            $companies = Company::all();
        }
        else
        {
            $companies = Favourite::where('user_id', $id)->get();
        }

        $array_datas = ['name', 'sector_id', 'id'];
        $array_titles = ['Negocio', 'Sector', ''];

        $datatable = $dt->datatable(
                                    'datatable_companies', $companies, $array_datas, null, null, $buscadores, 'admin.users.datatable.datatable_fav', $array_titles
                                );

        $script = $dt->script('datatable_companies', $buscadores);

        return ['datatable' => $datatable, 'script' => $script];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $getCompanies = Company::where('payed', 1)
                            ->where('enable_events', 1)
                            ->where('blocked', 0)
                            ->get();
                            
        $getCustomers = User::where('role', 'user')->get();

        $customers = [];
        $companies = [];

        foreach ($getCustomers as $customer)
        {
            $customers[$customer->id] = $customer->name . ' - ' . $customer->email;
        }

        foreach ($getCompanies as $company)
        {
            $companies[$company->id] = $company->name . ' - ' . $company->name_comercial;
        }

        $company_selected = session('company_selected');

        return view('admin.calendar.create', [
                                                'companies' => $companies,
                                                'customers' => $customers,
                                                'company_selected' => $company_selected
                                             ]);
    }

    public function getCrew(Request $request)
    {
        if ($request->ajax())
        {
            $company = Company::find($request->id_company);
            $services = $company->services;
            $crew = $company->crew()->where('visible', 1)->get();

            return view('admin.calendar.parts.crew_and_services', ['crew' => $crew, 'services' => $services]);
        }
    }

    /**
     * Load Calendar View.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCalendar(Request $request)
    {
        if ($request->ajax())
        {
            $events = [];
            $user = User::find($request->id);
            $services = Service::where('company_id', $user->company->id)->get();

            // Obtenemos los día bloqueados
            $events = $this->getEventUserBlocked($events , $request->id);

            $data = Event::where('user_id', $request->id)->with(['customer', 'service'])->get();

            foreach ($data as $key => $value)
            {
                $color = '#f05050';

                if ($value->status == 1)
                {
                    $color = '#50f050';
                }

                $title = '<strong>'.$value->title.'</strong>';
                
                if(
                    !is_null($value->customer) && (
                        Auth::user()->role == 'superadmin' || 
                        Auth::user()->role == 'operator' || 
                        (Auth::user()->role == 'admin' && \Auth()->user()->company_id == $value->service->company_id) ||
                        (Auth::user()->role == 'crew' && \Auth()->user()->id == $value->user_id) ||
                        (Auth::user()->role == 'user' && \Auth()->user()->id == $value->customer_id)
                    )
                ){
                    $title .= '<br/>' . $value->customer->name.' '.$value->customer->surname;
                }

                // if ((Auth::user()->role != 'user' && Auth::user()->role != 'admin') ||
                //     (Auth::user()->role == 'user' || Auth::user()->role == 'admin') && (Auth::user()->id == $value->customer_id))
                // {
                    // if (!is_null($value->customer))
                    // {
                    //     $title .= '<br/>' . $value->customer->name.' '.$value->customer->surname;
                    // }
                // }

                if ($value->customer_id == Auth::user()->id ||
                    (Auth::user()->user == 'superadmin' || Auth::user()->role == 'operator'))
                {
                    $events[] = Calendar::event(
                        $title,
                        false,
                        new \DateTime($value->start_date),
                        new \DateTime($value->end_date),
                        null,
                        // Add color and link on event
                        [
                            'color' => $color,
                            'firstDay' => 1,
                            'lang' => 'es',
                            'url' => route('calendar.show', $value->id),
                        ]
                    );
                }
                else
                {
                    $events[] = Calendar::event(
                        $title,
                        false,
                        new \DateTime($value->start_date),
                        new \DateTime($value->end_date),
                        null,
                        // Add color and link on event
                        [
                            'color' => $color,
                            'firstDay' => 1,
                            'lang' => 'es'
                        ]
                    );
                }

            }

            $free_days = $this->getEventCompanyFreeDays($events, $request->id);

            $dayRender = 'function( date, cell ) {
                                            // $(this).css("cursor", "pointer");

                                            // cell.on("click", function () {
                                            //     alert(cell.data("date"))
                                            // });

                                            if (cell.hasClass("fc-past"))
                                            {
                                                cell.addClass("fc-disabled");
                                            }

                                            ';

            for ($i = 0; $i < count($free_days); $i++)
            {
                $dayRender .=               '
                                            if (cell.hasClass("fc-' . $free_days[$i] . '"))
                                            {
                                                cell.addClass("fc-disabled");
                                            }
                                            ';
            }

            $dayRender .=                   '
                                        }';

            $calendar = Calendar::addEvents($events)
                                  ->setOptions([
                                        'firstDay' => 1,
                                        // 'businessHours' => [
                                        //   // days of week. an array of zero-based day of week integers (0=Sunday)
                                        //   'dow' => [ 1, 2, 3, 4, 5 ], // Monday - Thursday

                                        //   'start' => "10:00", // a start time (10am in this example)
                                        //   'end' => "18:00", // an end time (6pm in this example)
                                        // ]
                                        // Estas vistas no podemos ponerlas por defecto porque
                                        // necesitamos el plugin https://fullcalendar.io/docs/scheduler
                                        // que es de pago
                                        // 'defaultView' => 'timelineDay'
                                        // 'defaultView' => 'timelineWeek'
                                        'defaultView' => 'month',
                                        'header' => [
                                            'left' => 'prev,next today',
                                            'center' => 'title',
                                            'right' => 'basicWeek month listWeek'
                                        ],
                                        'allDaySlot' => false,
                                  ])->setCallbacks([
                                        // Esta función debería de ocurrir con cada celda cargada
                                        'dayRender' => $dayRender,
                                        'dayClick' => "function(date, jsEvent, view) {
                                            if ($('input[name=date_day]').length > 0)
                                            {
                                                $('input[name=date_day]').val($(this).data('date'))
                                            }

                                            if (! $(this).hasClass('fc-disabled'))
                                            {
                                                $('#modalEvent').modal('show');
                                            }

                                            modalSetTime($(this).data('date'));

                                            // alert('Clicked on: ' + date.format());

                                            // alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);

                                            // alert('Current view: ' + view.name);

                                            // // change the day's background color just for fun

                                          }
                                        ",
                                        // 'viewRender' => "function(view, element) {
                                        //     // $('.fc-day').css('background-color', 'rgba(202, 231, 200, 0.65)');
                                        // }",
                                        'eventRender' => "function (event, element, view) {
                                            classNames = event.className;

                                            for (i = 0; i < classNames.length; i++)
                                            {
                                                if ('disabled' == classNames[i])
                                                {
                                                    element.addClass('disabled');
                                                }
                                            }
                                            $(element).find('.fc-title').html('<br/>' + event.title);
                                            $(element).find('.fc-list-item-title').html(event.title);


                                        }",
                                        'eventAfterAllRender' => "function( view ) {
                                            var wait = setInterval(function () {
                                                if ($('#calendar-my-calendar').length > 0)
                                                {
                                                    clearInterval(wait);

                                                    var bg_elements = $('#calendar-my-calendar').find('.fc-bg');
                                                    var ske_elements = $('#calendar-my-calendar').find('.fc-bgevent-skeleton');

                                                    $.each(bg_elements, function () {
                                                        var ske_element = $(this).parentsUntil('fc-row').find('.fc-bgevent-skeleton');

                                                        if (ske_element.length > 0)
                                                        {
                                                            $.each(ske_element.find('td.disabled'), function () {
                                                                var index = $(this).prev().attr('colSpan');
                                                                console.log($(this).parentsUntil('.fc-row').parent().find('.fc-bg').find('td').eq(index));

                                                                $(this).parentsUntil('.fc-row').parent().find('.fc-bg').find('td').eq(index).addClass('fc-disabled')
                                                            });
                                                        }
                                                    });
                                                }
                                            });
                                        }"
                                  ]);

            $calendar->setId('my-calendar');

            return view('admin.calendar.parts.calendar_view', ['calendar' => $calendar])->render();
        }
    }

    private function getEventUserBlocked($events, $id)
    {
        $blocked = EventUserBlocked::where('user_id', $id)->get();

        foreach ($blocked as $key => $value)
        {
            if ($value->all_day)
            {
                $events[] = Calendar::event(
                    'Bloqueado completo',
                    true,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date),
                    2222,
                    // Add color and link on event
                    [
                        'color' => '#FFF',
                        'firstDay' => 1,
                        'lang' => 'es',
                        'rendering' => 'background',
                        'className' => 'disabled'
                    ]
                );
            }
            else
            {
                $color = '#000';

                $events[] = Calendar::event(
                    ' - ' . Carbon::parse($value->end_date)->format('H:i') . ' - Bloqueado',
                    false,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date),
                    null,
                    // Add color and link on event
                    [
                        'color' => $color,
                        'firstDay' => 1,
                        'lang' => 'es'
                    ]
                );
            }
        }

        return $events;
    }

    private function getEventCompanyFreeDays($events, $id)
    {
        $now = Carbon::today();
        $company = User::find($id)->company;

        $free_days = $this->getCompanyFreeDays($company->schedule);

        return $free_days;
    }

    private function getCompanyFreeDays($schedule)
    {
        $schedule = json_decode($schedule);
        $days = ['l' => 0, 'm' => 0, 'x' => 0, 'j' => 0, 'v' => 0, 's' => 0, 'd' => 0];

        foreach ($schedule as $group)
        {
            foreach ($group as $day => $value)
            {
                if ($value)
                {
                    $days[$day]++;
                }
            }
        }

        $days = array_filter($days, function ($value) {
            return $value < 1;
        });

        $days_of_week = [];

        foreach ($days as $day => $value)
        {
            switch ($day)
            {
                case 'l':
                    array_push($days_of_week, 'mon');

                    break;

                case 'm':
                    array_push($days_of_week, 'tue');

                    break;

                case 'x':
                    array_push($days_of_week, 'wed');

                    break;

                case 'j':
                    array_push($days_of_week, 'thu');

                    break;

                case 'v':
                    array_push($days_of_week, 'fri');

                    break;

                case 's':
                    array_push($days_of_week, 'sat');

                    break;

                case 'd':
                    array_push($days_of_week, 'sun');

                    break;
            }
        }
        // dd($days_of_week, $days_of_week == [6, 0]);

        return $days_of_week;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Cita creada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $service    = Service::find($request->service);
            $customer   = User::find($request->customer);
            $crew       = USer::find($request->crew);

            $event = new Event();

            $event->title        = $service->name;
            $event->user_id      = $request->crew;
            $event->customer_id  = $request->customer;
            $event->service_id   = $request->service;
            $event->status       = $request->status;
            $event->start_date   = Carbon::parse($request->start_date);
            $event->end_date     = Carbon::parse($request->start_date)->addMinutes($service->min);
            $event->service_json = json_encode($service->toArray());

            $event->save();

            $this->sendPushCreateEventJob($event);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Storing Event', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Error Storing Event', ['error' => $e]);
            dd('hola mundo2');
        }

        if ($error)
        {
            $message = 'Error al crear la cita.';
            $m_status = 'error';
        }

        return redirect()->route('calendar.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    private function sendPushCreateEventJob($event)
    {
        if (isset($event->customer->api_token) && $event->customer->api_token)
        {
            $time = Carbon::parse($event->start)->format('d/m H:i') . ' - ' . $event->service->name;

            $data = [
                        'tipo' => 'nueva_cita',
                        'token' => $event->customer->api_token,
                        'title' => $time,
                        'body' => 'Cita creada'
                    ];

            foreach ($event->customer->getTokens as $token)
            {
                dispatch(new PushCreateEventJob($token->push_token, $data));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::find($id);

        if ((Auth::user()->role == 'superadmin') ||
        	(Auth::user()->role == 'operator') ||
        	(Auth::user()->role == 'admin' && $event->service->company->admin->id == Auth::user()->id) ||
        	(Auth::user()->id == $event->user_id) ||
        	(Auth::user()->id == $event->customer_id))
        {
        	return view('admin.calendar.event', ['event' => $event]);
        }

        return redirect()->route('home.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::find($id);

        if ((Auth::user()->role == 'superadmin') ||
        	(Auth::user()->role == 'operator') ||
        	(Auth::user()->role == 'admin' && $event->service->company->admin->id == Auth::user()->id) ||
        	(Auth::user()->id == $event->user_id) ||
        	(Auth::user()->id == $event->customer_id))
        {
	        $companies = Company::all()->pluck('name', 'id')->toArray();
	        $customers = User::where('role', 'user')->pluck('name', 'id')->toArray();

	        if (isset($event->user))
	        {
	            $company = $event->user->company;

	            $services = $company->services;

	            $crews = $company->crew()->pluck('name', 'id');

	            $crew = $company->crew;

	            $crew_view = view('admin.calendar.parts.crew_and_services', ['crew' => $crew, 'services' => $services])->render();

	            return view('admin.calendar.edit', [
	                                                    'companies' => $companies,
	                                                    'customers' => $customers,
	                                                    'crew_view' => $crew_view,
	                                                    'crew'      => $crews,
	                                                    'event'     => $event
	                                                ]);
	        }

	        $message = 'El profesional no existe. No se puede acceder a la cita porque ya no es válida.';
	        $m_status = 'warning';

	        return redirect()->route('calendar.index')->with(['message' => $message, 'm_status' => $m_status]);
        }
        
        return redirect()->route('home.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = 'Cita modificada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $service    = Service::find($request->service);
            $crew       = USer::find($request->crew);

            $event = Event::find($id);

            $event->title        = $service->name;
            $event->user_id      = $request->crew;
            $event->service_id   = $request->service;
            $event->status       = 1;

            if ($request->start_date)
            {
                $event->start_date   = Carbon::parse($request->start_date);
                $event->end_date     = Carbon::parse($request->start_date)->addMinutes($service->min);
            }

            $event->service_json = json_encode($service->toArray());

            $event->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating Event', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Error Updating Event', ['error' => $e]);
            dd('hola mundo2');
        }

        if ($error)
        {
            $message = 'Error al modificar la cita.';
            $m_status = 'error';
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxUpdateEvent(Request $request, $id)
    {
        if ($request->ajax())
        {
            $this->update($request, $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = 'La cita ha sido eliminada con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $event = Event::find($id);

            $event->delete();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Deleting Event', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Deleting Event', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al eliminar la cita.';
            $m_status = 'error';
        }

        return redirect()->route('crew.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $event = Event::find($request->id);

                if ((Auth::user()->role != 'superadmin' && Auth::user()->role != 'operator') &&
                    $event->start_date <= Carbon::now())
                {
                    return -1;
                }

                $event->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting Event', ['error' => $e]);

                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting Event', ['error' => $e]);

                return 0;
            }
        }

        return 1;
    }

    /**
     * Obtenemos los días y las posiblidades de horas a las que añadir un evento.
     *
     * @param  Request  $request
     * @return response
     */
    public function dayTime(Request $request)
    {
        if ($request->ajax())
        {
            $crew = User::find($request->id);
            $service = Service::find($request->service);
            $editando = $request->has('event') ? Event::find($request->event) : null;
            
            //$company = $crew->company;
            //$schedule = json_decode($company->schedule);
            //$datetime = Carbon::parse($request->date);
            //$isEdit = (isset($request->isEdit)) ? true : false;

            $calendario = new \App\Helpers\Calendario($service, $crew, strtotime($request->date), $editando);
            return $calendario->getMoments();
            
            //return $this->getMoments($schedule, $datetime, $request->id, $request->service, $isEdit);

        }
    }

    // private function getMoments($schedule, $datetime, $id, $service, $isEdit)
    // {
    //     // Obtenemos el día en formato ISO (del 1 al 7)
    //     $day = $datetime->dayOfWeek;
        
    //     // Seleccionamos el día según inicial
    //     $select_day = $this->selectedDay($day);

    //     // Obtenemos el servicio
    //     $service = Service::find($service);
    //     if(is_null($service)){
    //         return [];
    //     }

    //     // Obtenemos todos los momentos de ese día
    //     $moments = $this->getAllDayMoments($schedule, $datetime, $select_day, $service);

    //     // Obtenemos todos los momentos en función de los eventos de ese día.
    //     // También empezamos a marcar los momentos útiles.
    //     $moments = $this->getEventsDayMoments($moments, $id, $isEdit, $service->min);

    //     // Marcamos qué momentos son útilies y cuales no
    //     $moments = $this->eventMomentWindows($moments, $id, $datetime, $select_day, $schedule, $service, $isEdit);

    //     return $moments;
    // }

    // private function selectedDay($day)
    // {
    //     $select_day = NULL;

    //     switch ($day)
    //     {
    //         case 0:
    //             $select_day = 'd';

    //             break;

    //         case 1:
    //             $select_day = 'l';

    //             break;

    //         case 2:
    //             $select_day = 'm';

    //             break;

    //         case 3:
    //             $select_day = 'x';

    //             break;

    //         case 4:
    //             $select_day = 'j';

    //             break;

    //         case 5:
    //             $select_day = 'v';

    //             break;

    //         case 6:
    //             $select_day = 's';

    //             break;
    //     }

    //     return $select_day;
    // }

    // private function getAllDayMoments($schedule, $datetime, $select_day, $service)
    // {
        
    //     $moments = [];

    //     $day = Carbon::parse($datetime)->toDateString();

    //     $start_time = Carbon::parse($day . ' ' . $schedule->horario_ini1->$select_day);
    //     $end_time   = Carbon::parse($day . ' ' . $schedule->horario_fin1->$select_day);

    //     while ($start_time < $end_time)
    //     {
            
    //         $_moment = $start_time->toDateTimeString();
    //         $start_time->addMinutes($service->min);

    //         if($start_time <= $end_time){
    //             $moments[] = $_moment;
    //         }

    //     }

    //     if ($schedule->horario_ini2->$select_day > $schedule->horario_ini1->$select_day)
    //     {
    //         $start_time = Carbon::parse($day . ' ' . $schedule->horario_ini2->$select_day);
    //         $end_time   = Carbon::parse($day . ' ' . $schedule->horario_fin2->$select_day);

    //         while ($start_time < $end_time)
    //         {
    //             $_moment = $start_time->toDateTimeString();
    //             $start_time->addMinutes($service->min);

    //             if($start_time <= $end_time){
    //                 $moments[] = $_moment;
    //             }
    //         }
    //     }

    //     return $moments;
    // }

    // private function getEventsDayMoments($moments, $id, $isEdit, $moment_duration = 0)
    // {
    //     $toReturn = [];

    //     $day_events = [];
    //     $day_blocks = [];

    //     if($moment_duration == 0 && count($moments) > 1){
    //         $moment_duration = (strtotime($moments[1]) - strtotime($moments[0])) / 60;
    //     }

    //     if(count($moments) > 0){

    //         $day = strtotime($moments[0]);

    //         $day_events = Event::where('user_id', $id)
    //                       ->whereBetween('start_date', [date('Y-m-d 00:00:00', $day), date('Y-m-d 23:59:59', $day)])
    //                       ->get();

    //         $day_blocks = EventUserBlocked::where('user_id', $id)
    //                       ->where(function ($query) use ($day) {
    //                             $query->where(function ($q) use ($day) {
    //                                 $q->where('all_day', 0)
    //                                   ->where('start_date', '>=', date('Y-m-d 00:00:00', $day))
    //                                   ->where('end_date', '<=', date('Y-m-d 23:59:59', $day));
    //                             })
    //                             ->orWhere(function ($q) use ($day) {
    //                                 $q->where('all_day', 1)
    //                                   ->where('start_date', 'LIKE', date('Y-m-d', $day) . ' %');
    //                             });
    //                         })
    //                         ->get();

    //     }

    //     foreach ($moments as $index => $moment)
    //     {
    //         // Obtenemos un momento y decimos
    //         // que tiene que estar activo
    //         $toReturn[$index]['time'] = $moment;
    //         $toReturn[$index]['disabled'] = false;

    //         $parsed_moment = Carbon::parse($moment);
    //         $end_parsed_moment = Carbon::parse($moment);
    //         $end_parsed_moment->addMinutes($moment_duration);

    //         // Comprobamos si ese momento está dentro
    //         // de un intervalo de tiempo de un evento

    //         // $event = Event::where('user_id', $id)
    //         //               ->where('start_date', '<=', Carbon::parse($moment))
    //         //               ->where('end_date', '>=', Carbon::parse($moment))
    //         //               ->first();

    //         $event = null;
    //         foreach($day_events as $e){
    //             //if($e->start_date <= $parsed_moment && $e->end_date > $parsed_moment){
    //             if($e->start_date < $end_parsed_moment && $e->end_date > $parsed_moment){
    //                 $event = $e;
    //                 break;
    //             }
    //         }

    //         // Si no encontramos un evento normal
    //         // buscamos si para ese momento hay un
    //         // evento de los que bloquean horas (no
    //         // contemplamos los que ocupan todo el día)

    //         // if (!$event)
    //         // {
    //         //     $event = EventUserBlocked::where('user_id', $id)
    //         //                              ->where('all_day', 0)
    //         //                              ->where('start_date', '<=', Carbon::parse($moment))
    //         //                              ->where('end_date', '>=', Carbon::parse($moment))
    //         //                              ->first();
    //         // }

    //         if(!$event){
    //             foreach($day_blocks as $e){
    //                 if($e->all_day == 1){
    //                     $event = $e;
    //                     break;
    //                 } else {
    //                     //if($e->start_date <= $parsed_moment && $e->end_date > $parsed_moment){
    //                     if($e->start_date < $end_parsed_moment && $e->end_date > $parsed_moment){
    //                         $event = $e;
    //                         break;
    //                     }
    //                 }
    //             }
    //         }

    //         // Si existe el evento y no estamos editando o si estamos editando,
    //         // tenemos fecha de inicio del evento y esta es igual al momento,
    //         // desactivamos que se pueda elegir este momento (hora)
    //         if (($event && (! $isEdit)) ||
    //             ($isEdit && isset($event->start_date) && $moment == $event->start_date))
    //         {
    //             $toReturn[$index]['disabled'] = true;
    //         }
    //     }

    //     return $toReturn;
    // }

    // /**
    //  * Desactivamos las horas que no deban puedan ser elegidas 
    //  * porque el nuevo evento a guardar no quepa en el intervalo
    //  *
    //  * @param  Request  $request
    //  * @return response
    //  */
    // private function eventMomentWindows($moments, $id, $datetime, $select_day, $schedule, $service, $isEdit)
    // {

    //     if (! $isEdit)
    //     {
    //         // Con esto desactivamos los momentos que van después
    //         // de la hora de inicio de la cita y que cubre el tiempo
    //         // de la misma. Si estamos editando esa cita, no es necesario
    //         // porque queremos cambiar la hora de la cita.
    //         for ($i = 0; $i < count($moments); $i++)
    //         {
    //             $time = Carbon::parse($moments[$i]['time']);
    //             $time->addMinutes($service->min);

    //             for ($j = $i; $j < count($moments); $j++)
    //             {
    //                 $moment = Carbon::parse($moments[$j]['time']);

    //                 if ($moments[$j]['disabled'] && $moment < $time)
    //                 {
    //                     $moments[$i]['disabled'] = true;

    //                     break;
    //                 }
    //             }
    //         }
    //     }

    //     $day = Carbon::parse($datetime)->toDateString();

    //     for ($i = 0; $i < count($moments); $i++)
    //     {
    //         if ($moments[$i]['disabled'] == false)
    //         {
    //             $time = Carbon::parse($moments[$i]['time']);
    //             $service_time = Carbon::parse($moments[$i]['time']);
    //             $service_time->addMinutes($service->min);

    //             $fin = Carbon::parse($day . ' ' . $schedule->horario_fin1->$select_day);

    //             if ($time < $fin && $service_time > $fin)
    //             {
    //                 $moments[$i]['disabled'] = true;
    //             }

    //             $fin = Carbon::parse($day . ' ' . $schedule->horario_fin2->$select_day);
                
    //             if ($time < $fin && $service_time > $fin)
    //             {
    //                 $moments[$i]['disabled'] = true;
    //             }
    //         }
    //     }

    //     return $moments;
    // }

    public function ajaxUpdate(Request $request)
    {
        if ($request->ajax())
        {
            $this->update($request, $request->id);
        }
    }

    public function goToCreate($id, Request $request)
    {
        session()->flash('company_selected', $id);

        return $this->create();
    }

    public function termSearch(Request $request)
    {
        if ($request->ajax())
        {
            $toReturn = [];

            $sectors = Sector::where('name', 'like', '%' . $request->term . '%')
                             ->limit(5)
                             ->get();

            foreach ($sectors as $sector)
            {
                $count = Company::payed()->where('sector_id', $sector->id)->count();

                $toReturn['sectors'][$sector->name] = $count;
            }

            $services = Service::where('name', 'like', '%' . $request->term . '%')
                               // ->limit(5)
                               ->has('company')
                               ->with('company')
                               ->get();
            $count = [];

            $toReturn['services'] = [];

            foreach ($services as $service)
            {
                $index = str_replace(' ', '_', $service->name);

                if (! array_key_exists($index, $toReturn['services']))
                {
                    $toReturn['services'][$index]['value'] = $service->name;
                    $toReturn['services'][$index]['count'] = 1;
                }
                else
                {
                    $toReturn['services'][$index]['count'] = ++$toReturn['services'][$index]['count'];
                }

            }

            $tags = CompanyTag::where('name', 'like', '%' . $request->term . '%')
                              ->limit(5)
                              ->get();

            foreach ($tags as $tag)
            {
                $count = CompanyTag::where('name', 'like', '%' . $tag->name . '%')
                                   ->count();

                $toReturn['tags'][$tag->name] = $count;
            }

            return view('admin.calendar.parts.term_search', ['toReturn' => $toReturn]);
        }
    }

    public function searchSpecial(Request $request)
    {
        switch ($request->type)
        {
            case 'sectors':
                $sectors = Sector::where('name', $request->value)->get();
                $companies = Company::payed();

                for ($i = 0; $i < count($sectors); $i++)
                {
                    if ($i == 0)
                    {
                        $companies = $companies->where('sector_id', $sectors[$i]->id);
                    }
                    else
                    {
                        $companies = $companies->orWhere('sector_id', $sectors[$i]->id);
                    }
                }

                $companies = $companies->get();

                break;
            
            case 'services':
                $companies = Company::payed()
                                    ->whereHas('services', function ($query) use ($request) {
                                        $query->where('name', $request->value);
                                    })
                                    ->get();

                break;
            
            case 'tags':
                $companies = Company::payed()->
                                    whereHas('tags', function ($query) use ($request) {
                                        $query->where('name', $request->value);
                                    })
                                    ->get();

                break;

            default:
                $companies = [];

                break;
        }

        return view('admin.calendar.parts.modal_search_list', ['companies' => $companies]);
    }

    public function nextEvents()
    {
    	$next_events = $this->getNextEventByUserRole(Auth::user());
        $datatable_events = $this->datatableNextEvents($next_events);

        return view('admin.calendar.index', [
                                                'datatable' => $datatable_events['datatable'],
                                                'script' => $datatable_events['script'],
                                                'search' => true,
                                                'datatable_companies' => NULL
                                            ]);
    }

    private function getNextEventByUserRole($user)
    {
    	$events = Event::where('start_date', '>=', Carbon::now());

    	if ($user->role == 'admin')
    	{
    		return $events->where(function ($query) use ($user) {
    							$query->where('user_id', $user->id);

					    		foreach ($user->company->crew as $crew)
					    		{
					    			if ($crew->id != $user->id)
					    			{
					    				$query = $query->orWhere('user_id', $crew->id);
					    			}
					    		}
				    		 })
				    		 ->has('customer')
				    		 ->has('service')
				    		 ->get();
    	}
    	elseif ($user->role == 'crew')
    	{
    		$events->where('user_id', $user->id)
    			   ->has('customer')
    			   ->has('service');
    	}
    	elseif ($user->role == 'user')
    	{
    		$events->where('customer_id', $user->id);
    	}

    	return $events->get();
    }

    private function datatableNextEvents($events)
    {
        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;
            
        $action = 'show';

        if (Auth::user()->role == 'crew')
        {
            $array_datas = ['service_id', 'user_id', 'user_id', 'user_id', 'start_date', 'end_date'];
            $array_titles = [
                                trans('app.common.service'),
                                trans('app.common.name'),
                                trans('app.common.email'), 
                                trans('app.common.phone'),
                                trans('app.common.init'),
                                trans('app.common.end')
                            ];

            $template = 'admin.dashboard.datatable.datatable_crew';
        }
        else
        {
            $array_datas = ['service_id', 'customer_id', 'user_id', 'user_id', 'start_date', 'end_date'];
            $array_titles = [
                                trans('app.common.service'),
                                trans('app.common.customer'), 
                                trans('app.common.crew'),
                                trans('app.common.company'),
                                trans('app.common.init'),
                                trans('app.common.end')
                            ];

            $template = 'admin.dashboard.datatable.datatable';
        	
        	$action = 'edit';
        }

        $datatable = $dt->datatable(
                                    'datatable_next_events', $events, $array_datas, $action, 'calendar', $buscadores, $template, $array_titles
                                );

        $script = $dt->script('datatable_next_events', $buscadores);

        return ['script' => $script, 'datatable' => $datatable];
    }
}
