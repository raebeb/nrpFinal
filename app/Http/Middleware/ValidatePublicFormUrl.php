<?php

namespace App\Http\Middleware;

use Closure;
use App\Country;
use App\Hospital;
use App\Service;

class ValidatePublicFormUrl
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
        if (
            Country::findOrFail($request->route('country')) && 
            Hospital::findOrFail($request->route('hospital')) &&
            Service::findOrFail($request->route('service'))
        ){
            return $next($request);
        }
    }
}
