<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;

/**
 * Session Controller for the Customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class SessionController extends BaseController
{
    public function __construct()
    {
        $this->middleware("customer")->except(["login"]);
    }

    /**
     * Logs in user and geneates jwt token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try
        {
            $data = $request->validate([
                "email" => "required|email|exists:customers,email",
                "password" => "required"
            ]);

            $jwtToken = Auth::guard("customer")
                ->setTTL(config("jwt.customer_jwt_ttl")) // Customer's JWT token time to live
                ->attempt($data);

            if (!$jwtToken) return $this->errorResponse($this->lang("login-error"), 401);

            $payload = [
                "token" => $jwtToken,
                "user" => auth()->guard("customer")->user()
            ];
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse($payload, $this->lang("login-success"));
    }

    /**
     * Logout the Customer
     * 
     * @return JsonResponse
     */
    public function logout()
    {
        try
        {
            auth()->guard("customer")->logout();
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang("logout-success"));
    }
}
