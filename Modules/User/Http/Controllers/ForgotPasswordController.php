<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Entities\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Exceptions\AdminNotFoundException;
use Modules\Customer\Exceptions\TokenGenerationException;

/**
 * Forgot Password controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ForgotPasswordController extends BaseController
{
    public function __construct(Admin $admin)
    {
        $this->model = $admin;
        $this->model_name = "Admin";

        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Generate a token and sends token in email for user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $this->validate($request, ["email" => "required|email"]);

            $admin = $this->model::where("email", $request->email)->firstOrFail();
            $response = $this->broker()->sendResetLink(["email" => $admin->email]);

            if ($response == Password::INVALID_TOKEN) throw new TokenGenerationException(__("core::app.users.token.token-generation-problem"));
            if ($response == Password::INVALID_USER) throw new AdminNotFoundException("Admin not found.");
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (TokenGenerationException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (AdminNotFoundException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage("Reset Link sent to your email {$admin->email}");
    }

    /**
     * Get the broker to be used during password reset
     * 
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admins');
    }
}
