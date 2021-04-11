<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;

/**
 * Session Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class SessionController extends BaseController
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except(['logout']);
    }

    /**
     * Logs in user and geneates jwt token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        Event::dispatch('admin.session.login.before');

        try
        {
            $data = $request->validate([
                "email" => "required|email|exists:admins,email",
                "password" => "required"
            ]);

            $jwtToken = Auth::guard("admin")
                ->setTTL(config("jwt.admin_jwt_ttl")) // Customer's JWT token time to live
                ->attempt($data);

            if (!$jwtToken) return $this->errorResponse($this->lang("login-error"), 401);

            $payload = [
                "token" => $jwtToken,
                "user" => auth()->guard("admin")->user()
            ];
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);

        }
        catch (\Exception  $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        Event::dispatch('admin.session.login.after', auth()->guard("admin")->user());
        return $this->successResponse($payload, $this->lang("login-success"));
    }

    /**
     * Logout the Admin
     * 
     * @return JsonResponse
     */
    public function logout()
    {
        Event::dispatch('admin.session.logout.before');
        try
        {
            $admin = auth()->guard('admin')->user();
            auth()->guard("admin")->logout();
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        Event::dispatch('admin.session.logout.after' , $admin);
        return $this->successResponseWithMessage($this->lang("logout-success"));
    }
}
