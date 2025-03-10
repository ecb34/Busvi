<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Company;
use App\User;
use App\Service;
use App\ServicesUser;
use App\Event;
use App\EventUserBlocked;
use App\FavouriteCrew;

use Calendar;
use Auth;
use Validator;
use Carbon\Carbon;
use Intervention\Image\ImageManager as InterImage;
use DataTables;

class CrewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->middleware('isAdmin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role == 'admin')
        {
            $services = Service::where('company_id', Auth::user()->company->id)->count();
        }
        else
        {
            $services = Service::count();
        }

        $disabled = true;

        if ($services)
        {
            $disabled = false;
        }

        $users = new User();

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        // $users = $users->where('role', 'crew')
        //                ->orWhere('role', 'admin');
        $users = $users->where(function ($query) {
            $query->where('role', 'crew')
                  ->orWhere('role', 'admin');
        });

        if (Auth::user()->role == 'admin')
        {
            $users = $users->where('company_id', Auth::user()->company_id);

            $users        = $users->get();
            $array_datas  = ['name', 'username', 'email', 'id'];
            $array_titles = ['Nombre', 'Username', 'email', ''];
        }
        else
        {
            $users        = $users->get();
            $array_datas  = ['name', 'username', 'email', 'id', 'id'];
            $array_titles = ['Nombre', 'Username', 'email', 'Negocio', ''];
        }

        $datatable = $dt->datatable(
                                    'datatable_users', $users, $array_datas, 'edit', 'crew', $buscadores, 'admin.crew.datatable.datatable_crews', $array_titles
                                );

        $script = $dt->script('datatable_users', $buscadores);

        return view('admin.crew.index', ['disabled' => $disabled, 'datatable' => $datatable, 'script' => $script, 'search' => $buscadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::all();
        $companies = Company::all()->filter(function ($value, $key) {
                                        return $value->services->isNotEmpty();
                                     })
                                   ->pluck('name', 'id')->toArray();

        return view('admin.crew.create', ['services' => $services, 'companies' => $companies]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = 'Profesional creado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required|min:5',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails())
            {
                $message = '';

                if ($validator->errors()->get('email'))
                {
                    $message .= $validator->errors()->get('email')[0];
                }
                elseif ($validator->errors()->get('password'))
                {

                    $message .= $validator->errors()->get('password')[0];
                }
                elseif ($validator->errors()->get('password_confirmation'))
                {
                    $message .= $validator->errors()->get('password_confirmation')[0];
                }
                elseif ($validator->errors()->get('username'))
                {
                    $message .= $validator->errors()->get('username')[0];
                }

                $m_status = 'error';
                $error = true;

                $request->session()->flash('status', 'validation_error');

                return redirect()->back()->with(['message' => $message, 'm_status' => $m_status])->withInput($request->all())->withErrors($validator);
            }

            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->dni = $request->dni;
            $user->role = 'crew';
            $user->password = bcrypt($request->password);

            if (Auth::user()->role == 'admin')
            {
                $user->company_id = Auth::user()->company_id;
            }
            else
            {
                $user->company_id = $request->company_id;
            }

            $user->save();

            // Si tiene imagen la guardamos
            if ($request->hasFile('img') && $request->file('img')->isValid())
            {
                $error = $this->storeCrewImg($request, $user);
            }

            if (! $this->saveUserServices($request, $user))
            {
                $user->forceDelete();

                $error = true;
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating User', ['error' => $e]);
            dd($e);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating User', ['error' => $e]);
            dd($e);
        }

        if ($error)
        {
            $message = 'Error al crear el profesional.';
            $m_status = 'error';
        }

        return redirect()->route('crew.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Almacena los servicios del usuario.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function saveUserServices($request, $user)
    {
        try
        {
            $new_services_users = $request->services;
            $actual_services_users = ServicesUser::where('user_id', $user->id)->get();

            if ($actual_services_users)
            {
                foreach ($actual_services_users as $services_user)
                {
                    $services_user->forceDelete();
                }
            }

            if ($new_services_users)
            {
                foreach ($new_services_users as $services_user)
                {
                    $new_services_user = new ServicesUser();
                    $new_services_user->user_id = $user->id;
                    $new_services_user->service_id = $services_user;

                    $new_services_user->save();
                }
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing User Services', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing User Services', ['error' => $e]);

            return 0;
        }

        return 1;
    }

    /**
     * Almacena el logo de la empresa.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private function storeCrewImg(Request $request, User $user)
    {
        try
        {
            $file_name = $this->getFileName($request->file('img'), $user->id);

            $image_resize = new InterImage;
            $image_resize = $image_resize->make($request->file('img')->getRealPath());
            $image_resize->fit(400, 400);

            if ($image_resize->save(public_path() . '/img/crew/' . $file_name))
            {
                \File::delete(public_path() . '/img/crew/' . $user->img);

                $user->img = $file_name;
            }

            $user->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Storing User Image', ['error' => $e]);

            return 1;
        }
        catch (Exception $e)
        {
            \Log::info('DB ERROR - Storing User Image', ['error' => $e]);

            return 1;
        }

        return 0;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    	$auth_company = NULL;
    	
    	if (Auth::user()->company)
    	{
    		$auth_company = Auth::user()->company->id;
        }
        
        $user = User::find($id);

        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
        {
            return redirect()->route('users.edit', $id);
        }
        elseif ($user->company->id != $auth_company)
        {
        	return redirect()->route('crew.index');
        }
        elseif (Auth::user()->role == 'user')
        {
        	return redirect()->route('users.edit', Auth::user()->id);
        }

        $services = Service::where('company_id', $user->company->id)->orderBy('order', 'asc')->get();
        $disabled = NULL;

        if ($services->isEmpty())
        {
            $disabled = 'disabled';
        }

        $calendar = $this->generateCalendar($id);

        $meta_title = $user->name;

        return view('admin.crew.edit', ['user' => $user, 'services' => $services, 'disabled' => $disabled, 'calendar' => $calendar, 'meta_title' => $meta_title]);
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
        $message = 'Profesional actualizado con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'sometimes|unique:users,email,' . $id,
                'username' => 'required|unique:users,username,' . $id
            ]);

            if ($validator->fails())
            {
                $message = '';

                if ($validator->errors()->get('email'))
                {
                    $message .= $validator->errors()->get('email')[0];
                }
                elseif ($validator->errors()->get('password'))
                {

                    $message .= $validator->errors()->get('password')[0];
                }
                elseif ($validator->errors()->get('password_confirmation'))
                {
                    $message .= $validator->errors()->get('password_confirmation')[0];
                }
                elseif ($validator->errors()->get('username'))
                {
                    $message .= $validator->errors()->get('username')[0];
                }

                $m_status = 'error';
                $error = true;

                $request->session()->flash('status', 'validation_error');

                return redirect()->back()->with(['message' => $message, 'm_status' => $m_status])->withInput($request->all())->withErrors($validator);
            }

            $user = User::find($id);
            $user->name = $request->name;
            $user->username = $request->username;
            $user->phone = $request->phone;
            $user->dni = $request->dni;

            if(\Auth::user()->role != 'crew'){
                $user->visible = $request->has('visible');
            }

            if (isset($request->email) && $user->email != $request->email)
            {
                $user->email = $request->email;
            }

            if (! $this->saveUserServices($request, $user))
            {
                $error = true;
            }

            // Si tiene imagen la guardamos
            if ($request->hasFile('img') && $request->file('img')->isValid())
            {
                $error = $this->storeCrewImg($request, $user);
            }
            else
            {
                $user->save();
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Updating User', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Updating User', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al actualizar el profesional.';
            $m_status = 'error';
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = 'Error al eliminar el profesional.';
        $m_status = 'error';

        try
        {
            $user = User::find($id);

            if ($user->role != 'admin' && $this->destroyServices($user))
            {
                \File::delete(public_path() . '/img/crew/' . $user->img);

                $user->delete();
                
                $message = 'Profesional eliminado con éxito.';
                $m_status = 'success';
            }
            elseif ($user->role == 'admin')
            {
                $message = 'No se puede eliminar al profesional porque es el administrador de un negocio. Deberas eliminar el negocio para eliminar al profesional.';
                $m_status = 'warning';
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Deleting User', ['error' => $e]);
        }
        catch (Exception $e)
        {
            \Log::info('Deleting User', ['error' => $e]);
        }

        return redirect()->route('crew.index')->with(['message' => $message, 'm_status' => $m_status]);
    }

    public function ajaxDestroy(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $user = User::find($request->id);

                if ($this->destroyServices($user))
                {
                    return 0;
                }

                \File::delete(public_path() . '/img/crew/' . $user->img);

                $user->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting User', ['error' => $e]);

                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting User', ['error' => $e]);

                return 0;
            }
        }

        return 1;
    }

    public function ajaxDestroyEventBlock(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $event_block = EventUserBlocked::find($request->id);

                $event_block->delete();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Deleting EventUserBlocked', ['error' => $e]);

                return 0;
            }
            catch (Exception $e)
            {
                \Log::info('Deleting EventUserBlocked', ['error' => $e]);

                return 0;
            }
        }

        return 1;
    }

    public function setFavoriteCrew(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                $favorite = FavouriteCrew::where('crew_id', $request->id)
                                     ->where('user_id', Auth::user()->id)
                                     ->first();

                if ($favorite)
                {
                    $favorite->delete();
                }
                else
                {
                    $favorite = new FavouriteCrew();
                    $favorite->user_id = Auth::user()->id;
                    $favorite->crew_id = $request->id;

                    $favorite->save();
                }
                
                return 1;
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                \Log::info('DB ERROR - Favouriting Crew', ['error' => $e]);
            }
            catch (Exception $e)
            {
                \Log::info('Favouriting Crew', ['error' => $e]);
            }
        }

        return 0;
    }

    private function destroyServices($user)
    {
        try
        {
            $services = ServicesUser::where('user_id', $user->id)->get();

            if ($services)
            {
                foreach ($services as $service)
                {
                    $service->delete();
                }
            }
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            \Log::info('DB ERROR - Deleting User', ['error' => $e]);

            return 0;
        }
        catch (Exception $e)
        {
            \Log::info('Deleting User', ['error' => $e]);

            return 0;
        }

        return 1;
    }

    public function blockEvent(Request $request, $id)
    {
        $user = User::find($id);

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $events       = $user->blockedEvents()->get();
        $array_datas  = ['start_date', 'all_day', 'end_date', 'id'];
        $array_titles = ['Inicio', 'Todo el día', 'Fin', ''];

        $datatable = $dt->datatable(
                                    'datatable_events', $events, $array_datas, null, null, $buscadores, 'admin.crew.datatable.datatable', $array_titles
                                );

        $script = $dt->script('datatable_events', $buscadores);

        $meta_title = $user->name;

        return view('admin.crew.blocked', ['user' => $user, 'datatable' => $datatable, 'script' => $script, 'search' => $buscadores, 'meta_title' => $meta_title]);
    }

    public function addBlockEvent($id, Request $request)
    {
        $message = 'Día/hora bloquead@ con éxito.';
        $m_status = 'success';
        $error = false;

        try
        {
        	$start = Carbon::parse($request->start_date);
        	$end = (! $request->end_date) ? NULL : Carbon::createFromFormat('Y-m-d H:i', $start->toDateString() . ' ' . $request->end_date);

            $event_block = new EventUserBlocked();

            $event_block->user_id    = $id;
            $event_block->all_day    = (! $request->all_day) ? 0 : $request->all_day;
            $event_block->text    	 = ($request->text) ? $request->text : NULL;
            $event_block->start_date = $start;
            $event_block->end_date   = $end;

            $event_block->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            $error = true;

            \Log::info('DB ERROR - Inserting EventUserBlocked', ['error' => $e]);
        }
        catch (Exception $e)
        {
            $error = true;

            \Log::info('Error Inserting EventUserBlocked', ['error' => $e]);
        }

        if ($error)
        {
            $message = 'Error al insertar el bloqueo de día/hora.';
            $m_status = 'error';
        }

        return redirect()->back()->with(['message' => $message, 'm_status' => $m_status]);
    }

    /**
     * Obtiene los ervicios de la empresa elegida.
     *
     * @param  company_id
     * @return string 'nombre'
     */
    public function ajaxGetServices(Request $request)
    {
        if ($request->ajax())
        {
            $services = Service::where('company_id', $request->id)->get();

            return view('admin.crew.parts.services_items', ['services' => $services])->render();
        }
    }

    /**
     * Obtiene el nombre de un archivo aunque sea muy largo.
     *
     * @param  file  $file
     * @return collection
     */
    private function getFileName($file)
    {
        return uniqid() . '.png';
    }

    /**
     * Generamos el calendario para el profesional.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function generateCalendar($id)
    {
        $events = [];
        $data = Event::where('user_id', $id)->get();

        $events = $this->getEventUserBlocked($events, $id);

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
                    (Auth::user()->role == 'admin' && !is_null($value->service) && \Auth()->user()->company_id == $value->service->company_id) ||
                    (Auth::user()->role == 'crew' && \Auth()->user()->id == $value->user_id) ||
                    (Auth::user()->role == 'user' && \Auth()->user()->id == $value->customer_id)
                )
            ){
                $title .= '<br/>' . $value->customer->name.' '.$value->customer->surname;
            }

            $events[] = Calendar::event(
                $title,
                false,
                new \DateTime($value->start_date),
                new \DateTime($value->end_date),
                null,
                // Add color and link on event
                [
                    // 'color' => $color,
                    'firstDay' => 1,
                    'lang' => 'es',
                    'url' => route('calendar.show', $value->id),
                ]
            );
        }

        $free_days = $this->getEventCompanyFreeDays($events, $id);

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
                                        if (! $(this).hasClass('fc-disabled'))
                                        {
                                            // alert('ahora funciona');
                                        }

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
                                        // console.log(jQuery.isArray(event.className));
                                        
                                        classNames = event.className;

                                        for (i = 0; i < classNames.length; i++)
                                        {
                                            if ('disabled' == classNames[i])
                                            {
                                                element.addClass('disabled');
                                            }
                                        }

                                        $(element).find('.fc-title').html('<br/>'+event.title);
                                        $(element).find('.fc-list-item-title').html(event.title);
                                        
                                        //     // console.log(element)
                                        //     // console.log(view)

                                        //     if ($.inArray('blocked', event.className) != '-1')
                                        //     {
                                        //         var index = element.parent().index();
                                        //         var el = $(element);
                                        //         element.parentsUntil('.fc-row').find('.fc-bg').find('td').eq(index).css('background-color', 'black');
                                        //     }


                                        //     // event.start is already a moment.js object
                                        //     // we can apply .format()
                                        //     var dateString = event.start.format('YYYY-MM-DD');
                                        //     var ev = $('[data-date=' + dateString + ']');

                                        //     // console.log(ev.attr('class'));
                                        //     // ev.addClass('fc-disabled');
                                        //     // ev.css('background-color', '#FFF');
                                    }",
                                    'eventAfterAllRender' => "function( view ) {
                                        var wait = setInterval(function () {
                                            console.log('hola mundo');

                                            if ($('#calendar-my-calendar').length > 0)
                                            {
                                                clearInterval(wait);

                                                var bg_elements = $('#calendar-my-calendar').find('.fc-bg');
                                                var ske_elements = $('#calendar-my-calendar').find('.fc-bgevent-skeleton');

                                                $.each(bg_elements, function (index) {
                                                    // var ske_element = $(this).parentsUntil('fc-row').find('.fc-bgevent-skeleton');

                                                    // if (ske_element.length > 0)
                                                    // {
                                                    //     console.log('bien');
                                                    //     $.each(ske_element.find('td.disabled'), function () {
                                                    //         var index = $(this).prev().attr('colSpan');
                                                    //         console.log(index);

                                                    //         ske_element.parent().find('.fc-bg').find('td').eq(index).addClass('fc-disabled')
                                                    //     });
                                                    // }

                                                    var ske_element = ske_elements.find('.fc-bgevent-skeleton').find('.fc-bg').find('td').eq(index).addClass('fc-disabled')

                                                    ske_element.parent().find('.fc-bg').find('td').eq(index).addClass('fc-disabled')
                                                });
                                            }
                                        });
                                    }"
                              ]);

        $calendar->setId('my-calendar');

        return $calendar;
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
                $text = ($value->text) ? $value->text : 'Bloqueado';

                $events[] = Calendar::event(
                    ' - ' . Carbon::parse($value->end_date)->format('H:i') . ' - ' . $text,
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

    public function getFichajes(Request $request, $crew_id){

        $user = \Auth::user();
        $crew = \App\User::find($crew_id);

        if(is_null($user) || is_null($crew)){
            abort(404);
        }

        switch($user->role){
            
            case 'admin':
                if($crew->company->id != $user->company->id){
                    abort(404);
                }
            break;
            
            case 'crew':
                if($crew->id != $user->id){
                    abort(404);
                }
            break;

            default:
                abort(404);
        }
        
        
        $fichajes = \App\Fichaje::where('user_id', $crew->id)->orderBy('inicio', 'desc');
        $datatable_fichajes = DataTables::of($fichajes);
        
        $datatable_fichajes->editColumn('inicio', function($fichaje){
            return date('j/n/y H:i', strtotime($fichaje->inicio));
        });

        $datatable_fichajes->editColumn('fin', function($fichaje){
            if(is_null($fichaje->fin)){
                return '';
            } else {
                return date('j/n/y H:i', strtotime($fichaje->fin));
            }
        });

        $datatable_fichajes->addColumn('duracion', function($fichaje){
            if(is_null($fichaje->fin)){
                return '';
            } else {

                $duracion = '';

                $inicio = strtotime($fichaje->inicio);
                $fin = strtotime($fichaje->fin);

                $segundos = $fin - $inicio;

                $horas = floor($segundos / (60 * 60));
                if($horas > 0){
                    $duracion .= $horas.'h ';
                }

                $segundos -= $horas * 60 * 60;
                $minutos = floor($segundos / 60);
                $duracion .= $minutos.'m ';

                return trim($duracion);

            }
        });

        $datatable_fichajes->addColumn('action', function($fichaje) use ($user) {
            $output = '';

            if($fichaje->posicion_inicio != ''){
                $posicion = json_decode($fichaje->posicion_inicio);
                if(!is_null($posicion)){
                    $url = 'http://maps.google.com/maps?z=18&q='.$posicion->lat.','.$posicion->lng;
                    $output .= '<a href="'.$url.'" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-map-marker"></i> inicio</a> ';
                }
            }

            if($fichaje->posicion_fin != ''){
                $posicion = json_decode($fichaje->posicion_fin);
                if(!is_null($posicion)){
                    $url = 'http://maps.google.com/maps?z=18&q='.$posicion->lat.','.$posicion->lng;
                    $output .= '<a href="'.$url.'" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-map-marker"></i> fin</a> ';
                }
            }

            if($user->role == 'admin'){
                $output .= '<button type="button" class="edit btn btn-xs btn-primary" fichaje="'.htmlentities(json_encode($fichaje)).'">Editar</button> ';
                $output .= '<button type="button" class="eliminar btn btn-xs btn-danger" fichaje="'.htmlentities($fichaje->id).'">Borrar</button>';
            }

            return $output;
        });

        $datatable_fichajes->rawColumns(['action']);

        return $datatable_fichajes->make(true);

    }

    public function postFichaje(Request $request){

        $user = \Auth::user();
        if($user->role != 'admin'){
            abort(404, 'Acceso denegado');
        }

        $crew = \App\User::find($request->crew_id);
        if(is_null($crew) || $crew->company->id != $user->company->id){
            abort(404, 'Profesional desconocido');
        }

        $fichaje = \App\Fichaje::find($request->fichaje_id);
        if(is_null($fichaje) || $fichaje->user_id != $crew->id){
            abort(404, 'Fichaje desconocido');
        }

        $fichaje->inicio = $request->inicio;
        $fichaje->fin = $request->fin;

        $fichaje->save();

        return response()->json($request->all());

    }

    public function postDeleteFichaje(Request $request){

        $user = \Auth::user();
        if($user->role != 'admin'){
            abort(404, 'Acceso denegado');
        }

        $crew = \App\User::find($request->crew_id);
        if(is_null($crew) || $crew->company->id != $user->company->id){
            abort(404, 'Profesional desconocido');
        }

        $fichaje = \App\Fichaje::find($request->fichaje_id);
        if(is_null($fichaje) || $fichaje->user_id != $crew->id){
            abort(404, 'Fichaje desconocido');
        }

        $fichaje->delete();

        return response()->json($request->all());

    }

    public function postInformeFichajes(Request $request){

        $user = \Auth::user();
        if($user->role != 'admin'){
            abort(404, 'Acceso denegado');
        }

        $crew = \App\User::find($request->crew_id);
        if(is_null($crew) || $crew->company->id != $user->company->id){
            abort(404, 'Profesional desconocido');
        }

        $inicio = strtotime($request->fecha_inicio);
        $fin = strtotime('+1 day', strtotime($request->fecha_fin));

        $fichajes = \App\Fichaje::where('user_id', $crew->id)->whereNotNull('fin')->whereBetween('inicio', [date('Y-m-d', $inicio), date('Y-m-d', $fin)])->orderBy('inicio', 'asc')->get();

        $pdf = new \App\Helpers\InformeFichajesPdf($crew, $inicio, $fin, $fichajes);
        return \Response::make($pdf->Output('informe.pdf', 'D'), 200, array('content-type'=>'application/pdf'));

    }

}
