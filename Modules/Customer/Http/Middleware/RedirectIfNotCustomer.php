<?php

namespace Modules\Customer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Traits\ApiResponseFormat;

class RedirectIfNotCustomer
{
    use ApiResponseFormat;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'customer')
    {
        if (! Auth::guard($guard)->check()) {
            return $this->errorResponse(400,"Unauthenticated user");
        } else {
            if (Auth::guard($guard)->user()->status == 0) {
                Auth::guard($guard)->logout();
                return $this->errorResponse(400,"Customer disabled");
            }
        }
        return $next($request);
    }
}
