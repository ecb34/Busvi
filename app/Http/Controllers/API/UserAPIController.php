<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Validator;
use Response;
use Auth;
use File;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Pagination\LengthAwarePaginator;
use Mail;

use App\Mail\UserMail;
use App\User;
use App\Company;
use App\Event;
use App\Evento;
use App\Reserva;
use App\Sector;
use App\Service;
use App\Favourite;
use App\FavouriteCrew;
use App\PushToken;
use App\EventUserBlocked;
use App\Post;

use App\Jobs\PushCreateEventJob;


class UserAPIController extends Controller
{
    private $params;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->params = json_decode($request->getContent(), true);

        if (! $this->params)
        {
            return response()->json(['msg' => trans('api.error_constructor_get_params')], 500);
        }

        $this->setLang($this->params['lang']);
    }

    /**
     * Make user login.
     *
     * @return response
     */
    public function login()
    {
        $this->setLang($this->params['lang']);

        $validator = Validator::make($this->params, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
        {
            return response()->json(['msg' => $validator->errors()->first()], 500);
        }

        if (filter_var($this->params['username'], FILTER_VALIDATE_EMAIL))
        {
	        $credentials = [
	            'email'	   => $this->params['username'],
	            'password' => $this->params['password']
	        ];
        }
        else
        {
	        $credentials = [
	            'username' => $this->params['username'],
	            'password' => $this->params['password']
	        ];
        }

        $push_token = $this->getPushToken($this->params['push_token']);

        if (Auth::attempt($credentials))
        {
            $this->userToPushToken($push_token, Auth::user()->id);

            if (! Auth::user()->api_token)
            {
                Auth::user()->generateAPIToken();
            }

            if (Auth::user()->role == 'user' or (Auth::user()->company->payed && !Auth::user()->company->blocked &&
                (Auth::user()->role == 'crew' || Auth::user()->role == 'admin')))
            {
                $values = [
                            'token'     => Auth::user()->api_token,
                            'rol'       => Auth::user()->role,
                            'username'  => Auth::user()->username,
                            'mis_cheques' => Auth::user()->chequesRegaloRecibidos,
                ];

                if(\Auth::user()->role == 'admin' || \Auth::user()->role == 'crew'){
                    $values['events'] = Auth::user()->company->type == 1 && Auth::user()->company->enable_events;
                    $values['fichajes'] = Auth::user()->company->type == 1 && Auth::user()->company->enable_fichajes;
                    $values['reservas'] = Auth::user()->company->type == 1 && Auth::user()->company->enable_reservas;
                }

                return response()->json($values, 200);
            }
        }

        return response()->json(['msg' => trans('api.login_incorrecto')], 500);
    }

    /**
     * Make user logout.
     *
     * @return response
     */
    public function logout()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            if (isset($this->params['push_token']) && $this->params['push_token'])
            {
                $push_token = PushToken::where('push_token', $this->params['push_token'])
                                       ->first();

                if ($push_token)
                {
                    $push_token->user_id = NULL;

                    $push_token->save();
                }
            }
        }

        Auth::logout();

        return response()->json([], 200);
    }

    /**
     * Check user check_login.
     *
     * @return response
     */
    public function check_login()
    {
        $user = User::where('api_token', $this->params['api_token'])
                    ->where('username', $this->params['username'])
                    ->first();

        $push_token = $this->getPushToken($this->params['push_token']);

        if ($user && ($user->role == 'user' || ($user->company->payed && !$user->company->blocked &&
            ($user->role == 'crew' || $user->role == 'admin'))))
        {
            $this->userToPushToken($push_token, $user->id);

            $values = [
                'token'     => $user->api_token,
                'rol'       => $user->role,
                'username'  => $user->username,
            ];

            if($user->role == 'admin' || $user->role == 'crew'){
                $values['events'] = $user->company->type == 1 && $user->company->enable_events;
                $values['fichajes'] = $user->company->type == 1 && $user->company->enable_fichajes;
                $values['reservas'] = $user->company->type == 1 && $user->company->enable_reservas;
                $values['cheques'] = $user->company->type == 1 && $user->company->accept_cheque_regalo;
                if($user->role == 'admin'){
                    $values['company_eventos'] = $user->company->accept_eventos;
                }

            }

            return response()->json($values, 200);
        }

        return response()->json(['msg' => trans('api.error_check_login')], 500);
    }

    /**
     *
     *
     * @return response
     */
    public function password_reset()
    {
        $user = User::where('email', $this->params['email'])->first();

        if ($user)
        {
            $response = Password::sendResetLink(['email' => $user->email], function (Message $message) {
                $message->subject($this->getEmailSubject());
            });

            switch ($response)
            {
                case Password::RESET_LINK_SENT:
                    return response()->json(['success' => 'success'], 200);

                    break;

                case Password::INVALID_USER:
                    return response()->json(['error' => 'error'], 500);

                    break;
            }
        }

        return response()->json(['error' => 'error'], 500);
    }

    public function register()
    {
        $push_token = $this->getPushToken($this->params['push_token']);

        $user = User::where('username', $this->params['username'])
                    ->orWhere('email', $this->params['email'])
                    ->first();

        if (! $user)
        {
            try
            {
                $user = new User();

                $user->name     = $this->params['name'];
                $user->surname  = $this->params['surname'];
                $user->username = $this->params['email'];
                $user->email    = $this->params['email'];
                $user->phone    = $this->params['phone'];
                $user->role     = 'user';
                $user->password = bcrypt($this->params['password']);

                $user->save();

                $user->generateAPIToken();

                $this->userToPushToken($push_token, $user->id);

                Mail::to($user->email)->send(new UserMail($user));
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                return response()->json(['error' => $e], 500);
            }
            catch (Exception $e)
            {
                return response()->json(['error' => $e], 500);
            }

            $values = [
                        'token'     => $user->api_token,
                        'rol'       => $user->role,
                        'username'  => $user->username
                      ];

            return response()->json($values, 200);
        }

        return response()->json(['msg' => trans('api.error_register')], 500);
    }

    public function edit()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            return response()->json(['user' => $user], 200);
        }

        return response()->json(['msg' => trans('api.error_edit')], 500);
    }

    public function update()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            try
            {
                $user->name     = $this->params['name'];
                $user->surname  = $this->params['surname'];
                $user->address  = $this->params['address'];
                $user->city     = $this->params['city'];
                $user->cp       = $this->params['cp'];
                $user->birthday = Carbon::parse($this->params['birthday']);
                $user->genere   = ($this->params['genere'] == -1) ? null : $this->params['genere'];
                $user->phone    = $this->params['phone'];

                if ($user->email != $this->params['email'])
                {
                    $validator = Validator::make($this->params['email'], [
                        'email' => 'required|unique:users'
                    ]);

                    if ($validator->fails())
                    {
                        return response()->json(['msg' => trans('api.error_update_email')], 500);
                    }

                    $user->email = $this->params['email'];
                    $user->username = $this->params['email'];
                }

                $user->save();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                return response()->json(['msg' => trans('api.error_update')], 500);
            }
            catch (Exception $e)
            {
                return response()->json(['msg' => trans('api.error_update')], 500);
            }

            return response()->json(['user' => $user], 200);
        }

        return response()->json(['msg' => trans('api.error_update')], 500);
    }

    public function updatePassword()
    {
        $user = $this->validToken($this->params['token']);

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $this->params['old_pass']]))
        {
            try
            {
                $validator = Validator::make($this->params, [
                    'old_pass' => 'required',
                    'pass' => 'required|min:5',
                    'repass' => 'required|same:pass',
                ]);

                if ($validator->fails())
                {
                    return response()->json(['msg' => $validator->errors()], 500);
                }

                $user->password = bcrypt($this->params['pass']);

                $user->save();
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                return response()->json(['msg' => trans('api.error_update_password')], 500);
            }
            catch (Exception $e)
            {
                return response()->json(['msg' => trans('api.error_update_password')], 500);
            }

            return response()->json(['user' => $user], 200);
        }

        return response()->json(['msg' => trans('api.error_update_password_error_actual_password')], 500);
    }

    public function sectors()
    {
        if ($this->validToken($this->params['token']))
        {
            return Sector::all();
        }

        return response()->json(['msg' => trans('api.error_sectors')], 500);
    }

    public function company()
    {
        if ($this->validToken($this->params['token']))
        {
            $company = Company::where('id', $this->params['company_id'])
                              ->with('sector', 'admin', 'services', 'crew.serviceUsers')
                              ->first();

            $company->addVisit();

            if ($company)
            {
                $gallery = $this->getGallery($company->id);
                $gallery_info = $this->getGallery($company->id, true);
            } else {
                $gallery = [];
                $gallery_info = [];
            }

            return response()->json(['company' => $company, 'gallery' => $gallery, 'gallery_info' => $gallery_info], 200);
        }

        return response()->json(['msg' => trans('api.error_company')], 500);
    }

    public function companies()
    {
        if ($this->validToken($this->params['token']))
        {
            $companies = Company::payed();

            if (isset($this->params['term']))
            {
                $companies = $this->findCompaniesByTag($companies, $this->params['term']);
            }

            if (isset($this->params['sector_id']))
            {
                $companies = $this->findCompaniesBySectorId($companies, $this->params['sector_id']);
            }

            $companies = $companies->get();

            $paginated = $this->companiesByDistance($companies, $this->params['lat'], $this->params['lon']);

            return response()->json(['companies' => $paginated], 200);
        }

        return response()->json(['msg' => trans('api.error_companies')], 500);
    }

    public function eventos()
    {
        if ($this->validToken($this->params['token']))
        {
            $eventos = Evento::where('validado',1)->where('desde', '>=', Carbon::now())->with('company','organizador','categoria');

            if (isset($this->params['term'])){
                $terms = explode(' ', trim($this->params['term']));

                $todos = false;
                foreach($terms as $term){
                    if(strtolower($term) == 'evento' || strtolower($term) == 'eventos'){
                        $todos = true;
                    }
                }

                if(!$todos){

                    $eventos->where(function($query) use ($terms) {
                        foreach ($terms as $term) {
                            $query->where(function ($q) use ($term){
                                $q->where('nombre','like' ,  '%'.$term . '%')
                                    ->orWhere('descripcion','like' ,  '%'.$term . '%')
                                    ->orWhere('desde','like' ,  '%'.$term . '%')
                                    ->orWhere('hasta','like' ,  '%'.$term . '%')
                                    ->orWhereHas('company' , function ($query) use ($term) {
                                        $query->where('name','like', '%' . $term . '%');
                                    })
                                    ->orWhereHas('organizador' , function ($query) use ($term) {
                                        $query->where('name','like', '%' . $term . '%')
                                            ->orWhere('surname','like', '%' . $term . '%');
                                    })
                                    ->orWhereHas('categoria' , function ($query) use ($term) {
                                        $query->where('nombre','like', '%' . $term . '%');
                                    });
                            });
                                
                        }
                    });    

                }

            }

            $eventos = $eventos->get();
            $paginated = $this->eventsByDistance($eventos, $this->params['lat'], $this->params['lon']);
            return response()->json(['eventos' => $paginated], 200);
            
        }

        return response()->json(['msg' => trans('api.error_companies')], 500);
    }

    public function favourites()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            $favourites = $this->getFavorites($user);

            return response()->json([
                                        'favourites' => $favourites['companies'], 
                                        'favourites_crew' => $favourites['crew']
                                    ], 200);
        }

        return response()->json(['msg' => trans('api.error_favourites')], 500);
    }

    public function addFavourite()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            if (isset($this->params['company_id']))
            {
                $favorite = Favourite::where('user_id', $user->id)
                                     ->where('company_id', $this->params['company_id']);
            }
            elseif (isset($this->params['crew_id']))
            {
                $favorite = FavouriteCrew::where('user_id', $user->id)
                                         ->where('crew_id', $this->params['crew_id']);
            }

            $favorite = $favorite->first();

            if (! $favorite)
            {
                if (isset($this->params['company_id']))
                {
                    $favorite = new Favourite();
                    $favorite->user_id = $user->id;
                    $favorite->company_id = $this->params['company_id'];
                }
                elseif (isset($this->params['crew_id']))
                {
                    $favorite = new FavouriteCrew();
                    $favorite->user_id = $user->id;
                    $favorite->crew_id = $this->params['crew_id'];
                }

                if (! $favorite->save())
                {
                    return response()->json(['msg' => trans('api.error_add_favourite')], 500);
                }
            }

            $favourites = $this->getFavorites($user);

            return response()->json([
                                        'favourites' => $favourites['companies'], 
                                        'favourites_crew' => $favourites['crew']
                                    ], 200);
        }

        return response()->json(['msg' => trans('api.error_add_favourite')], 500);
    }

    public function removeFavourite()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            if (isset($this->params['company_id']))
            {
                $favorite = Favourite::where('user_id', $user->id);
                $favorite = $favorite->where('company_id', $this->params['company_id']);
            }
            elseif (isset($this->params['crew_id']))
            {
                $favorite = FavouriteCrew::where('user_id', $user->id);
                $favorite = $favorite->where('crew_id', $this->params['crew_id']);
            }

            $favorite = $favorite->first();

            if ($favorite)
            {
                $favorite->delete();

                $favourites = $this->getFavorites($user);

                return response()->json([
                                            'favourites' => $favourites['companies'], 
                                            'favourites_crew' => $favourites['crew']
                                        ], 200);
            }
        }

        return response()->json(['msg' => trans('api.error_remove_favourite')], 500);
    }

    public function eventList()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            $now = Carbon::now()->setTimezone('Europe/Madrid');

            $events = Event::where('customer_id', $user->id)
                           ->where('start_date', '>=', $now)
                           ->with(['user' => function ($query) {
                                $query->with('company');
                           }, 'service'])
                           ->orderBy('start_date', 'ASC')
                           ->get();

            return response()->json(['events' => $events], 200);
        }

        return response()->json(['msg' => trans('api.error_event_list')], 500);
    }

    public function history()
    {
        $user = $this->validToken($this->params['token']);
        
        if ($user)
        {
            
            $now = Carbon::now()->setTimezone('Europe/Madrid');

            $term = '';
            if(isset($this->params['term']) && trim($this->params['term']) != ''){
                $term = trim($this->params['term']);
            }

            $events = Event::where('customer_id', $user->id)
                           ->where('start_date', '<', $now)
                           ->where(function($q) use ($term){

                                $q->where('title', 'like', '%'.$term.'%');

                                $q->orWhereHas('service', function($q_service) use ($term){
                                    $q_service->where('name', 'like', '%'.$term.'%');
                                });

                                $q->orWhereHas('user', function($q_user) use ($term){
                                    $q_user->where('name', 'like', '%'.$term.'%')
                                        ->orWhereHas('company', function($q_company) use ($term){
                                            $q_company->where('name', 'like', '%'.$term.'%');
                                            $q_company->orWhere('name_comercial', 'like', '%'.$term.'%');
                                            $q_company->orWhere('description', 'like', '%'.$term.'%');
                                        });
                                });

                           })
                           
                           ->with(['user.company', 'service']);

            $events = $events->orderBy('start_date', 'DESC')->get();

            $page = isset($this->params['page']) ? $this->params['page'] : 1;
            $perPage = isset($this->params['perPage']) ? $this->params['perPage'] : 100;

            $paginated = $this->arrayPaginator($events->toArray(), $page, $perPage);

            return response()->json(['events' => $paginated], 200);
        }

        return response()->json(['msg' => trans('api.error_history')], 500);
    }

    public function moments()
    {
        $user = $this->validToken($this->params['token']);
        $service = Service::find($this->params['service_id']);
        $event = isset($this->params['event_id']) ? Event::find($this->params['event_id']) : null;

        if ($user && $service)
        {
            $crew_id = $this->params['crew_id'];
            $month = $this->params['month'];
            $year = $this->params['year'];

            $crew = User::find($crew_id);
            $company = $crew->company;

            if (! $company->payed)
            {
                return response()->json(['msg' => trans('api.error_moments_company_no_payed')], 500);
            }

            $schedule = json_decode($company->schedule);

            $day = Carbon::now()->day;

            if (Carbon::today()->month != $month)
            {
                $day = '1';
            }

            $first_date = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);

            if ($first_date->isLastOfMonth())
            {
                $next_date = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);
            }
            else
            {
                $next_date = Carbon::createFromFormat('Y-m-d', $year . '-' . ($month + 1) . '-1');
            }

            $period = CarbonPeriod::create($first_date, $next_date);

            $moments = [];
            
            foreach ($period as $date)
            {

                //$moments[] = $this->getMoments($schedule, $date, $crew_id, $service, false);

                $calendario = new \App\Helpers\Calendario($service, $crew, strtotime($date->toDateString().' 00:00:00'), $event);
                $moments[] = $calendario->getMoments();

            }

            return response()->json(['moments' => $moments], 200);
        }

        return response()->json(['msg' => trans('api.error_moments')], 500);
    }

    public function createEvent()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            try
            {
                $service    = Service::find($this->params['service_id']);
                $crew       = USer::find($this->params['crew_id']);

                $event = new Event();

                $event->title        = $service->name;
                $event->customer_id  = $user->id;
                $event->user_id      = $this->params['crew_id'];
                $event->service_id   = $this->params['service_id'];
                $event->status       = 1;
                $event->start_date   = Carbon::parse($this->params['datetime']);
                $event->end_date     = Carbon::parse($this->params['datetime'])->addMinutes($service->min);
                $event->service_json = json_encode($service->toArray());

                $event->save();

                $qty_push = $this->sendPushCreateEventJob($event, $user->role);

                return response()->json([
                                            'event' => $event,
                                            'events' => $this->eventList(),
                                            'qty_push' => $qty_push
                                        ], 200);
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                $error = true;

                \Log::info('DB ERROR - Storing Event FROM API', ['error' => $e]);
                return response()->json(['msg' => $e], 500);
            }
            catch (Exception $e)
            {
                $error = true;

                \Log::info('Error Storing Event FROM API', ['error' => $e]);
                return response()->json(['msg' => $e], 500);
            }
        }

        return response()->json(['msg' => trans('api.error_create_event')], 500);
    }

    public function updateEvent()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            try
            {
                $event = Event::find($this->params['event_id']);

                if ($event)
                {
                    $event->start_date   = Carbon::parse($this->params['datetime']);
                    $event->end_date     = Carbon::parse($this->params['datetime'])->addMinutes($event->service->min);

                    $event->save();

                    $qty_push = $this->sendPushCreateEventJob($event, $user->role);

                    return response()->json([
                                                'event' => $event,
                                                'events' => $this->eventList(),
                                                'qty_push' => $qty_push
                                            ], 200);
                }
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                $error = true;

                \Log::info('DB ERROR - Updating Event FROM API', ['error' => $e]);
                return response()->json(['msg' => $e], 500);
            }
            catch (Exception $e)
            {
                $error = true;

                \Log::info('Error Updating Event FROM API', ['error' => $e]);
                return response()->json(['msg' => $e], 500);
            }
        }

        return response()->json(['msg' => trans('api.error_update_event')], 500);
    }

    public function deleteEvent()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            try
            {
                $event = Event::find($this->params['event_id']);

                if ($event)
                {
                    $event->delete();

                    return response()->json([], 200);
                }
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                $error = true;

                \Log::info('DB ERROR - Deleting Event FROM API', ['error' => $e]);
                return response()->json(['msg' => $e], 500);
            }
            catch (Exception $e)
            {
                $error = true;

                \Log::info('Error Deleting Event FROM API', ['error' => $e]);
                return response()->json(['msg' => $e], 500);
            }
        }

        return response()->json(['msg' => trans('api.error_delete_event')], 500);
    }

    public function getEventById()
    {
        $user = $this->validToken($this->params['token']);

        if(!isset($this->params['event_id'])){
            $this->params['event_id'] = 0;
        }

        if(!isset($this->params['reserva_id'])){
            $this->params['reserva_id'] = 0;
        }

        if ($user)
        {

            $response = [];

            $event = Event::where('id', $this->params['event_id'])
                          ->where(function($q) use ($user){
                                $q->where('user_id', $user->id)
                                  ->orWhere('customer_id', $user->id);
                           })
                           ->with(['user' => function ($query) {
                                $query->with('company');
                           }, 'service'])
                          ->first();

            if(!is_null($event)){
                $response['event'] = $event;
            }

            $reserva = Reserva::where('id', $this->params['reserva_id'])
                        ->with(['user', 'turno.company.admin'])
                        ->where(function($q) use ($user){
                            return $q->where('user_id', $user->id)->orWhereHas('turno.company.admin', function($q1) use ($user){
                                $q1->where('id', $user->id);
                            });
                        })
                        ->first();

            if(!is_null($reserva)){
                $response['reserva'] = $reserva;
            }

            if (count($response) > 0)
            {
                return response()->json($response, 200);
            }

        }

        return response()->json(['msg' => trans('api.error_get_event_by_id')], 500);
    }

    public function getDown()
    {
        $user = $this->validToken($this->params['token']);

        if ($user)
        {
            $user->delete();
                
            return response()->json([], 200);
        }

        return response()->json(['msg' => trans('api.error_get_down')], 500);
    }

    public function page()
    {
        
        $post = Post::where('slug', $this->params['slug'])->first();
            
        if ($post)
        {
            return response()->json(['title' => $post->title, 'content' => $post->body], 200);
        }

        return response()->json(['msg' => trans('api.error_slug')], 500);
    }



    /**
     * ----------------------------------------------------------------------
     *  Métodos Privados
     * ----------------------------------------------------------------------
     */

    // private function getMoments($schedule, $datetime, $id, $service, $isEdit)
    // {
        
    //     // Obtenemos el día en formato ISO (del 1 al 7)
    //     $day = $datetime->dayOfWeek;
        
    //     // Seleccionamos el día según inicial
    //     $select_day = $this->selectedDay($day);
        
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
    //         //               ->where('start_date', '<=', $parsed_moment)
    //         //               ->where('end_date', '>=', $parsed_moment)
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
    //         //                              ->where(function ($query) use ($moment) {
    //         //                                     $query->where(function ($q) use ($moment) {
    //         //                                             $q->where('all_day', 0)
    //         //                                               ->where('start_date', '<=', $parsed_moment)
    //         //                                               ->where('end_date', '>=', $parsed_moment);
    //         //                                           })
    //         //                                           ->orWhere(function ($q) use ($moment) {
    //         //                                             $q->where('all_day', 1)
    //         //                                               ->where('start_date', 'LIKE', $parsed_moment->format('Y-m-d') . '%');
    //         //                                           });
    //         //                              })
    //         //                              ->first();
    //         //
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
    //             unset($toReturn[$index]);
    //             // $toReturn[$index]['disabled'] = true;
    //         }
    //     }

    //     return $toReturn;
    // }

    /**
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
    //             if (array_key_exists($i, $moments))
    //             {
    //                 $time = Carbon::parse($moments[$i]['time']);
    //                 $time->addMinutes($service->min);

    //                 for ($j = $i; $j < count($moments); $j++)
    //                 {
    //                     if (array_key_exists($j, $moments))
    //                     {
    //                         $moment = Carbon::parse($moments[$j]['time']);

    //                         if ($moments[$j]['disabled'] && $moment < $time)
    //                         {
    //                             unset($moments[$i]);
    //                             // $moments[$i]['disabled'] = true;

    //                             break;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     $day = Carbon::parse($datetime)->toDateString();

    //     for ($i = 0; $i < count($moments); $i++)
    //     {
    //         if (array_key_exists($i, $moments) && ($moments[$i]['disabled'] == false))
    //         {
    //             if (array_key_exists($j, $moments))
    //             {
    //                 $time = Carbon::parse($moments[$i]['time']);
    //                 $service_time = Carbon::parse($moments[$i]['time']);
    //                 $service_time->addMinutes($service->min);

    //                 $fin = Carbon::parse($day . ' ' . $schedule->horario_fin1->$select_day);

    //                 if ($time < $fin && $service_time > $fin)
    //                 {
    //                     unset($moments[$i]);
    //                     // $moments[$i]['disabled'] = true;
    //                 }

    //                 $fin = Carbon::parse($day . ' ' . $schedule->horario_fin2->$select_day);

    //                 if ($time < $fin && $service_time > $fin)
    //                 {
    //                     unset($moments[$i]);
    //                     // $moments[$i]['disabled'] = true;
    //                 }
    //             }
    //         }
    //     }

    //     return $moments;
    // }

    private function validToken($token)
    {
        $user = User::where('api_token', $token)->first();

        if ($user)
        {
            return $user;
        }

        return FALSE;
    }

    private function setLang($lang)
    {
        if (isset($lang))
        {
            \App::setLocale($lang);
        }
    }

    private function getPushToken($pushToken)
    {
        $push_token = NULL;

        if (isset($pushToken) && $pushToken)
        {
            $push_token = PushToken::where('push_token', $pushToken)
                                   ->first();

            if (! $push_token)
            {
                $push_token = new PushToken;

                $push_token->push_token = $pushToken;
                $push_token->date = Carbon::now()->setTimezone('Europe/Madrid');

                $push_token->save();
            }
        }

        return $push_token;
    }

    private function userToPushToken($push_token, $id)
    {
        if ($push_token && $id)
        {
            $push_token->user_id = $id;

            $push_token->save();
        }
    }

    private function sendPushCreateEventJob($event, $role)
    {
        $time = Carbon::parse($event->start)->format('d/m H:i') . ' - ' . $event->service->name;

        if ($role == 'user')
        {
            $token = $event->user->api_token;
            $push_tokens = $event->user->getTokens;
        }
        else
        {
            $token = $event->customer->api_token;
            $push_tokens = $event->customer->getTokens;
        }

        $data = [
                    'tipo' => 'nueva_cita',
                    'event_id' => $event->id,
                    'token' => $token,
                    'title' => $time,
                    'body' => 'Se ha creado una nueva cita: ' . $event->service->name
                ];

        $qty_push = 0;

        foreach ($push_tokens as $token)
        {
            dispatch(new PushCreateEventJob($token->push_token, $data));

            $qty_push++;
        }

        return $qty_push;
    }

    private function getFavorites($user)
    {
        $favourites = $user->favourites()->with('company')->get();

        $companies = collect();

        foreach ($favourites as $fav)
        {
            $companies->push($fav->company);
        }

        $sorted = $companies->sortBy(function ($company) {
            return $company->name_comercial;
        });

        $crews = collect();

        foreach ($user->my_favourites as $fav)
        {
            if($fav->crew->visible){
                $crews->push($fav->crew()->with(['serviceUsers', 'company'])->first());
            }
        }

        $sorted_crew = $crews->sortBy(function ($crew) {
            return $crew->name;
        });

        return ['companies' => $sorted, 'crew' => $sorted_crew];
    }

    private function findCompaniesByTag($companies, $term)
    {
        return $companies->where(function($q) use ($term) {
                            $q->where('name', 'like', '%' . $term . '%')
                              ->orWhere('name_comercial', 'like', '%' . $term . '%')
                              ->orWhere('description', 'like', '%' . $term . '%')
                              ->orWhereHas('services', function ($query) use ($term) {
                                 $query->where('name', 'like', '%' . $term . '%');
                              })
                              ->orWhereHas('tags', function ($query) use ($term) {
                                 $query->where('name', 'like', '%' . $term . '%');
                              })
                              ->orWhereHas('sector', function ($query) use ($term) {
                                 $query->where('name', 'like', '%' . $term . '%');
                              });
                         });
                         
    }

    private function findCompaniesBySectorId($companies, $term)
    {
        return $companies->where('sector_id', $term);
    }

    private function companiesByDistance($companies, $latitudeFrom, $longitudeFrom)
    {
         $distance = [];
        foreach ($companies as $company)
        {
            if ($company->lat && $company->long)
            {
                $earthRadius = 6371000;

                $latFrom = deg2rad($latitudeFrom);
                $lonFrom = deg2rad($longitudeFrom);
                $latTo = deg2rad($company->lat);
                $lonTo = deg2rad($company->long);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                $km = ($angle * $earthRadius) / 1000;
                $km = round($km, 2);

                $company->setAttribute('km', $km);

                $distance[] = ['km' => $km, 'company' => $company];
            }
            else{
                $distance[] = ['km' => 'Sin datos', 'company' => $company];
            }
        }

        $sorted_distances = $sorted_companies = [];

        if ($distance)
        {
            $sorted_distances = $this->quicksort($distance);
        }

        foreach ($sorted_distances as $element)
        {
            $sorted_companies[] = $element['company'];
        }

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $perPage = isset($this->params['perPage']) ? $this->params['perPage'] : 100;

        return $this->arrayPaginator($sorted_companies, $page, $perPage);
    }

    private function eventsByDistance($eventos, $latitudeFrom, $longitudeFrom)
    {
        $distance = [];
        $sorted_eventos = [];
        foreach ($eventos as $evento)
        {
            if ($evento->lat && $evento->long)
            {
                $earthRadius = 6371000;

                $latFrom = deg2rad($latitudeFrom);
                $lonFrom = deg2rad($longitudeFrom);
                $latTo = deg2rad($evento->lat);
                $lonTo = deg2rad($evento->long);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                $km = ($angle * $earthRadius) / 1000;
                $km = round($km, 2);

                $evento->setAttribute('km', $km);

                $distance[] = ['km' => $km, 'evento' => $evento];
            }
            else{
              $distance[] = ['km' => 'Sin datos', 'evento' => $evento]; 
            }
        }

        $sorted_distances = $sorted_events = [];

        if ($distance)
        {
            $sorted_distances = $this->quicksort($distance);
        }

        foreach ($sorted_distances as $element)
        {
            $sorted_events[] = $element['evento'];
        }

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $perPage = isset($this->params['perPage']) ? $this->params['perPage'] : 100;

        return $this->arrayPaginator($sorted_events, $page, $perPage);
    }

    /**
     * Devuelve un array ordenado por sus claves --RECURSIVO--
     *
     * @return \Illuminate\Http\Response
     */
    private function quicksort($array)
    {
        // Creamos arrays para la para los valores mayores y menores
        $left = $right = [];

        // Si el array tiene menos de 2 elementos
        // directamente devolvemos el array
        if (count($array) < 2)
        {
            return $array;
        }

        // Ordenamos el array.
        // Para ello primero cogemos el primer elemento
        $pivot_key = key($array);
        $pivot = array_shift($array);

        // Metemos cada valor en el array que le corresponde
        for ($i = 0; $i < count($array); $i++)
        {
            if ($array[$i]['km'] <= $pivot['km'])
            {
                $left[] = $array[$i];
            }
            elseif ($array[$i]['km'] > $pivot['km'])
            {
                $right[] = $array[$i];
            }
        }

        // Creamos el array a devolver, que es la suma de los tres arrays:
        // el de menores (al que se le vuelve a aplicar esta función) +
        // el elemento que acabamos de coger como pivote +
        // el de mayore (al que también aplicamos esta función)
        $to_return = array_merge($this->quicksort($left), [$pivot_key => $pivot], $this->quicksort($right));

        return $to_return;
    }

    private function arrayPaginator($array, $page = 1, $perPage = 100)
    {
        $offset = ($page * $perPage) - $perPage;

        $paginator = new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true), // Sólo recojemos lo que queremos
            count($array), // Total de items
            $perPage, // Items por página
            $page // Página actual
        );

        return $paginator;
    }

    /**
     * Devuelve un array con los nombres de las imagenes de la galería.
     *
     * @param  int  id
     * @return array
     */
    private function getGallery($id, $return_object = false)
    {
        
        $toReturn = [];
        $company = Company::find($id);
        foreach($company->gallery as $image){
            $path = 'img/companies/galleries/' . $id . '/original/' . $image->filename;
            if(file_exists(public_path($path))){
                if(!$return_object){
                    $toReturn[] = asset($path);
                } else {
                    $image->path = asset($path);
                    $toReturn[] = $image;
                }
            }
        }

        return $toReturn;

    }

    /**
     * Obtiene el nombre de un archivo aunque sea muy largo.
     *
     * @param  file  $file
     * @return string 'nombre'
     */
    private function getFileName($file)
    {
        return uniqid() . '.png';
    }
}
