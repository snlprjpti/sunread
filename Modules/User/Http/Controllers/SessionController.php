<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\BaseController;

/**
 * Admin user session controller
 */
class SessionController extends BaseController
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['login','logout']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $remember = request('remember');
        $jwtToken = null;
        if (!$jwtToken = Auth::guard('admin')->attempt(request()->only('email', 'password'))) {
           return $this->errorResponse(401,"Invalid email or password");
        }
        $admin = auth()->guard('admin')->user();
        $payload = [
            'token' => $jwtToken,
            'user' => $admin
        ];
        return $this->successResponse(200, $payload , "Logged in successfully");
    }

    public function logout()
    {
        auth()->guard('admin')->logout();
        return $this->successResponse(200,null, "User logged out successfully");
    }


}