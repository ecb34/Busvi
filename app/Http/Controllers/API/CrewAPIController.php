<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;
use Response;
use Auth;
use Mail;
use App\Mail\UserMail;

use App\User;
use App\Company;
use App\Event;
use App\EventUserBlocked;
use App\Sector;

class CrewAPIController extends Controller
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
            return response()->json(['msg' => trans('api.login_incorrecto')], 500); 
        }

        $this->setLang($this->params['lang']);
    }

    /**
     * Check user login.
     * 
     * @return response
     */
    public function info()
    {
        $crew = User::where('api_token', $this->params['token'])
                    ->with('serviceUsers')
                    ->first();

        if ($crew && $crew->company->payed &&
            ($crew->role == 'crew' || $crew->role == 'admin'))
        {
            return response()->json(['user' => $crew], 200);
        }

        return response()->json(['msg' => trans('api.error_get_info_crew')], 500);
    }

    /**
     * Return list event between 2 dates.
     * 
     * @return response
     */
    public function listEvents()
    {
        $crew = $this->validToken($this->params['token']);

        $date = Carbon::createMidnightDate($this->params['year'], $this->params['month'], 1, 'Europe/Madrid');
        $date_end = Carbon::createMidnightDate($this->params['year'], $this->params['month'] + 1, 1, 'Europe/Madrid');

        if ($crew && $date)
        {
            $events = Event::where('start_date', '>=', $date)
                           ->where('end_date', '<=', $date_end)
                           ->where('user_id', $crew->id)
                           ->with('customer')
                           ->with('service')
                           ->get();
            
            $blocked = EventUserBlocked::where('start_date', '>=', $date)
                                       ->where(function ($query) use ($date_end) {
                                            $query->where('end_date', '<=', $date_end)
                                                  ->orWhere('end_date', NULL);
                                       })
                                       ->where('user_id', $crew->id)
                                       ->get();

            return response()->json(['events' => $events, 'blocked' => $blocked], 200);
        }

        return response()->json(['msg' => trans('api.error_list_events')], 500);
    }

    /**
     * Create Crew Event.
     * 
     * @return response
     */
    public function blockedNew()
    {
        $crew = $this->validToken($this->params['token']);

        if ($crew)
        {
            $blocked = new EventUserBlocked();

            $blocked->user_id    = $crew->id;
            $blocked->all_day    = (! $this->params['all_day']) ? 0 : $this->params['all_day'];
            $blocked->text       = ($this->params['text']) ? $this->params['text'] : NULL;
            $blocked->start_date = Carbon::parse($this->params['start_date']);
            $blocked->end_date   = (!$this->params['end_date']) ? NULL : Carbon::parse($this->params['end_date']);

            $blocked->save();

            return response()->json(['blocked' => $blocked], 200);
        }

        return response()->json(['msg' => trans('api.error_blocked_new')], 500);
    }

    /**
     * Remove Crew Event.
     * 
     * @return response
     */
    public function blockedDelete()
    {
        $crew = $this->validToken($this->params['token']);

        $bloqued_id = $this->params['id'];
        
        $blocked = EventUserBlocked::find($bloqued_id);

        if ($blocked->user_id == $crew->id)
        {
            $blocked->delete();

            return response()->json([], 200);
        }

        return response()->json(['msg' => trans('api.error_blocked_delete')], 500);
    }

    private function validToken($token)
    {
        $user = User::where('api_token', $token)->first();

        if ($user)
        {
            return $user;
        }

        return NULL;
    }

    private function setLang($lang)
    {
        if (isset($lang))
        {
            \App::setLocale($lang);
        }
    }
}
