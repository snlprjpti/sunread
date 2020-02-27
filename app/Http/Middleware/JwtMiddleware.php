<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Modules\Core\Traits\ApiResponseFormat;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    use ApiResponseFormat;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return $this->errorResponse(400, ['status' => 'Token is Invalid']);

            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return $this->errorResponse(400, ['status' => 'Token is Expired']);
            } else {
                return $this->errorResponse(400, ['status' => 'Token is Invalid']);;
            }
        }
        return $next($request);
    }
}