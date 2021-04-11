<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Exceptions\AdminNotFoundException;
use Modules\User\Exceptions\TokenGenerationException;

/**
 * Reset Password controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ResetPasswordController extends BaseController
{
    public function __construct(Admin $admin)
    {
        $this->model = $admin;
        $this->model_name = "Admin";

        parent::__construct($this->model, $this->model_name);
    }

    /**
     * OPTIONAL route: using frontend routes in future
     * Display the password reset token is valid
     * If no token is present, display the error
     * 
     * @param  string|null $token
     * @return JsonResponse
     */
    public function create($token = null)
    {
        return $token
            ? $this->successResponse(['token' => $token])
            : $this->errorResponse(trans('core::app.users.token-missing'), 400);
    }

    /**
     * Store new password of the admin
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $data = $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            $response = $this->broker()->reset($data, function ($admin, $password) {
                $this->resetPassword($admin, $password);
            });

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

        return $this->successResponseWithMessage(__("core::app.users.users.password-reset-success"));
    }

    /**
     * Get the broker to be used during password reset.
     * 
     * @return PasswordBroker
     */
    protected function broker()
    {
        return Password::broker('admins');
    }

    /**
     * Reset the given customer password.
     *
     * @param $admin
     * @param  string $password
     * @return void
     */
    protected function resetPassword($admin, $password)
    {
        $admin->password = Hash::make($password);
        $admin->setRememberToken(Str::random(60));
        $admin->save();
    }
}
