<?php

namespace App\Http\Middleware;
use Closure;
class PreflightResponse
{
    public function handle($request, Closure $next)
    {
        if ($request->getMethod() === "OPTIONS") {
            return response('')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
