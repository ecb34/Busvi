<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class companyPayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->company)
        {
            if (Auth::user()->company->blocked || ! Auth::user()->company->payed)
            {
                if (Auth::user()->role == 'admin')
                {
                    return redirect()->route('companies.payment', Auth::user()->company->id);
                }
                elseif (Auth::user()->role == 'crew')
                {
                    return redirect()->route('companies.payment_crew', Auth::user()->company->id);
                }
            }
        }

        return $next($request);
    }
}
