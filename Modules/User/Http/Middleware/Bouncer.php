<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Core\Traits\ApiResponseFormat;

/**
 * Bouncer Middleware for Admin resource
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */

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

        if (!Auth::guard($guard)->check()) return $this->errorResponse("Unauthenticated User." , 401);
        if(!$this->checkIfAuthorized($request)) return $this->errorResponse("Unauthorised access." , 403);

        return $next($request);
    }

    /**
     * Checks authorisation for particular resources
     * @param $request
     * @return bool
     */
    public function checkIfAuthorized($request)
    {
        if ( !($role = auth()->guard('admin')->user()->role) ) return false;
        if ($role->permission_type == 'all') return true;

        return bouncer()->allow(Route::currentRouteName());
    }
}
