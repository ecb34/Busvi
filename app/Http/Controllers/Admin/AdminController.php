<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;

use App\Company;
use App\Event;
use App\User;
use App\Favourite;
use App\FavouriteCrew;

class AdminController extends Controller
{
    public function home()
    {
    	$events = $this->getEventByUserRole(Auth::user());
    	$next_events = $this->getNextEventByUserRole(Auth::user());
        $companies_fav = $this->getCompanisFavByUserRole(Auth::user());
        $crew_fav = $this->getCrewFavByUserRole(Auth::user());
        
    	$datatable_events = $this->datatableEvents($events);
    	$datatable_next_events = $this->datatableNextEvents($next_events);
        $datatable_companies_fav = $this->datatableFavourites($companies_fav);
        $datatable_crew_fav = $this->datatableFavouritesCrew($crew_fav);

    	return view('admin.dashboard.index', [
    											'datatable_events' => $datatable_events,
    											'datatable_next_events' => $datatable_next_events,
                                                'datatable_companies_fav' => $datatable_companies_fav,
                                                'datatable_crew_fav' => $datatable_crew_fav,
                                                'events' => $events,
                                                'next_events' => $next_events,
                                                'companies_fav' => $companies_fav,
                                                'crew_fav' => $crew_fav
    										 ]);
    }

    private function getEventByUserRole($user)
    {
    	if ($user->role == 'operator' || $user->role == 'superadmin')
    	{
    		return Event::all();
    	}
    	elseif ($user->role == 'admin')
    	{
    		$events = Event::where('user_id', $user->id);

    		foreach ($user->company->crew as $crew)
    		{
    			if ($crew->id != $user->id)
    			{
    				$events = $events->orWhere('user_id', $crew->id);
    			}
    		}

    		return $events->get();
    	}
    	elseif ($user->role == 'crew')
    	{
    		return Event::where('user_id', $user->id)->get();
    	}
    	else
    	{
    		return Event::where('customer_id', $user->id)->get();
    	}
    }

    private function getNextEventByUserRole($user)
    {
    	$events = Event::where('start_date', '>=', Carbon::today());

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

    private function getCompanisFavByUserRole($user)
    {
        if ($user->role == 'operator' || $user->role == 'superadmin')
        {
            return Company::has('favourite')->get();
        }
        elseif ($user->role == 'admin')
        {
            return Favourite::where('company_id', $user->company_id)->get();
        }
        else
        {
            return Company::whereHas('favourite', function ($query) use ($user) {
                              $query->where('user_id', $user->id);
                          })
                          ->get();
        }
    }

    private function getCrewFavByUserRole($user)
    {
        if ($user->role == 'operator' || $user->role == 'superadmin')
        {
            return User::where('role', 'crew')
                       ->orWhere('role', 'admin')
                       ->has('favourites')
                       ->get();
        }
        elseif ($user->role == 'admin')
        {
            return User::where('company_id', Auth::user()->company_id)
                       ->has('favourites')
                       ->get();
        }
        else
        {
            return User::has('isFavourite')->get();
        }
    }

    private function datatableEvents($events)
    {
        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;
        
        $action = 'show';

        if (Auth::user()->role == 'crew')
        {
            $array_datas = ['service_id', 'user_id', 'user_id', 'user_id', 'start_date'];
            $array_titles = [
                                trans('app.common.service'),
                                trans('app.common.name'),
                                trans('app.common.email'), 
                                trans('app.common.phone'),
                                trans('app.common.init')
                            ];

            $template = 'admin.dashboard.datatable.datatable_crew';
        }
        else
        {
            $array_datas = ['service_id', 'customer_id', 'user_id', 'user_id', 'start_date'];
            $array_titles = [
                                trans('app.common.service'),
                                trans('app.common.customer'), 
                                trans('app.common.crew'),
                                trans('app.common.company'),
                                trans('app.common.init')
                            ];

            $template = 'admin.dashboard.datatable.datatable';
        	$action = 'edit';
        }

        $datatable = $dt->datatable(
                                    'datatable_events', $events, $array_datas, $action, 'calendar', $buscadores, $template, $array_titles
                                );

        $script = $dt->script('datatable_events', $buscadores);

        return ['script' => $script, 'datatable' => $datatable];
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

    private function datatableFavourites($favourites)
    {
        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $array_datas = ['name_comercial'];
        $array_titles = ['Negocio'];
        
        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
        {
            $array_datas = ['name_comercial', ''];
            $array_titles = [
                                trans('app.common.company'),
                                trans('app.common.favourites')
                            ];
        }
        
        $datatable = $dt->datatable(
                                    'datatable_favourites', $favourites, $array_datas, 'show', 'companies', $buscadores, 'admin.dashboard.datatable.datatable_fav', $array_titles
                                );

        $script = $dt->script('datatable_favourites', $buscadores);

        return ['script' => $script, 'datatable' => $datatable];
    }

    private function datatableFavouritesCrew($favourites)
    {

        $dt = new \App\Http\Controllers\DatatableController();

        $buscadores = false;

        $array_datas = ['name', 'name_comercial', ''];
        $array_titles = [
                            trans('app.common.name'),
                            trans('app.common.company'),
                            trans('app.common.favourites')
                        ];

        foreach($favourites as $fav){
            $fav->id = $fav->company->id; // así enlazo guarramente el item con la company
        }
        
        $datatable = $dt->datatable(
                                    'datatable_favourites_crew', $favourites, $array_datas, 'show', 'companies', $buscadores, 'admin.dashboard.datatable.datatable_fav_crew', $array_titles
                                );

        $script = $dt->script('datatable_favourites_crew', $buscadores);

        return ['script' => $script, 'datatable' => $datatable];
    }
}
