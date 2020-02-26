<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Controllers\BaseController;

/**
 * Session Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class SessionController extends BaseController
{


    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except(['logout']);
    }

    /**
     * Logs in user and geneates jwt token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse(400, $validator->errors());
        }
        $jwtToken = null;
        if (!$jwtToken = Auth::guard('admin')->attempt(request()->only('email', 'password'))) {
            return $this->errorResponse(401, "Invalid email or password");
        }

        $admin = auth()->guard('admin')->user();
        $payload = [
            'token' => $jwtToken,
            'user' => $admin
        ];
        return $this->successResponse(200, $payload, "Logged in successfully");
    }

    /**
     * Logout the Admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->guard('admin')->logout();
        return $this->successResponse(200, null, "User logged out successfully");
    }
}