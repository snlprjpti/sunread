<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Exceptions\TokenGenerationException;
use Modules\User\Entities\Admin;

/**
 * Forgot Password controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ForgotPasswordController extends BaseController
{

    use SendsPasswordResetEmails;


    /**
     *  Generate a token and sends token in email for user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, ['email' => 'required|email']);

            $email = $request->get('email');
            $admin = Admin::where('email', $email)->first();
            if (!$admin) {
                return $this->errorResponse("Missing email", 400);
            }

            $response = $this->broker()->sendResetLink($email);
            if ($response != Password::RESET_LINK_SENT) {
                throw new TokenGenerationException();
            }

            return $this->successResponseWithMessage($admin, "Reset Link sent to your email {$admin->email}", 200);


        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get the broker to be used during password reset
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admins');
    }
}