<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Core\Traits\ApiResponseFormat;

class Bouncer
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
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if (! Auth::guard($guard)->check()) {
            return $this->errorResponse(400, "UnAuthenticated User");
        }
        $isAuthorised = $this->checkIfAuthorized($request);

        if(!$isAuthorised){
            return $this->errorResponse(401, "Unauthorised access");
        }
        return $next($request);
    }

    public function checkIfAuthorized($request)
    {
        if (! $role = auth()->guard('admin')->user()->role)
            return false;
        if ($role->permission_type == 'all') {
            return true ;
        } else {
            return bouncer()->allow(Route::currentRouteName());
        }
    }
}
