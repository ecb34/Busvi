<?php

namespace App\Http\Middleware;

use Closure;

class reservasEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){

        if(!is_null(auth()->user()->company) && auth()->user()->company->payed && auth()->user()->company->type == 1 && auth()->user()->company->enable_reservas){
            return $next($request);
        }
        
        return redirect()->route('home.index');

    }
}
