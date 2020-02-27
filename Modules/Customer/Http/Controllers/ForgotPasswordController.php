<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;

/**
 * Forgot Password controller for the Customer
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
            $validator = Validator::make($request->all(), ['email' => 'required|email']);
            if ($validator->fails()) {
                return $this->errorResponse(400, $validator->errors());
            }

            $email = $request->get('email');
            $admin = Admin::where('email', $email)->first();

            if (!$admin) {
                return $this->errorResponse(400, "Email does not exist");
            }
            $response = $this->broker()->sendResetLink(request(['email']));

            if ($response == Password::RESET_LINK_SENT) {
                return $this->successResponse(200, $admin, "Reset Link sent to your email {$admin->email}");
            }
            return $this->errorResponse(400, $admin, "Unable to generate token. Try again later");
        } catch (\Exception $e) {
            return $this->errorResponse(400, $e->getMessage());
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