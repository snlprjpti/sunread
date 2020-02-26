<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
        try{
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $jwtToken = null;
        if (!$jwtToken = Auth::guard('admin')->attempt(request()->only('email', 'password'))) {
            return $this->errorResponse(400, "Invalid email or password");
        }
        $admin = auth()->guard('admin')->user();
        $payload = [
            'token' => $jwtToken,
            'user' => $admin
        ];
        return $this->successResponse(200, $payload, "Logged in successfully");
        }catch (ValidationException $exception){
            return $this->errorResponse(400, $exception->getMessage());
        }
        catch ( \Exception  $exception){
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * Logout the Admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try{
            auth()->guard('admin')->logout();
            return $this->successResponse(200, null, "User logged out successfully");
        }catch (\Exception $exception){
            return $this->errorResponse(400,$exception->getMessage());
        }

    }
}