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
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $jwtToken = null;
            $admin_jwt_ttl = config('jwt.admin_jwt_ttl');
            if (!$jwtToken = Auth::guard('admin')->setTTl($admin_jwt_ttl)->attempt(request()->only('email', 'password'))) {
                return $this->errorResponse(trans('core::app.users.users.login-error'), 400);
            }
            $admin = auth()->guard('admin')->user();
            $payload = [
                'token' => $jwtToken,
                'user' => $admin
            ];
            return $this->successResponse($payload, trans('core::app.users.users.login-success'),200);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->getMessage(), 422);

        } catch (\Exception  $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Logout the Admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->guard('admin')->logout();
            return $this->successResponseWithMessage(trans('core::app.users.users.logout-success'));

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }
}