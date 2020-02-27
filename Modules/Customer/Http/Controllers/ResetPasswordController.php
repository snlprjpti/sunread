<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;

/**
 * Reset Password controller for the Customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ResetPasswordController extends BaseController
{
    use ResetsPasswords;


    /**
     * Display the password reset token is valid
     * If no token is present, display the error
     * @param  string|null $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($token = null)
    {
        if (!$token) {
            return $this->errorResponse(400, "Missing token");
        }
        return $this->successResponse(200, $payload = ['token' => $token]);
    }

    /**
     *
     * Store new password of the admin
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            $response = $this->broker()->reset(
                request(['email', 'password', 'password_confirmation', 'token']), function ($customer, $password) {
                $this->resetPassword($customer, $password);
            });

            if ($response == Password::PASSWORD_RESET) {
                return $this->successResponse(200, null, "User Password reset success");
            }
            return $this->errorResponse(400, "Invalid token");
        } catch(ValidationException $exception){
            return $this->errorResponse(400,$exception->errors());
        }catch (\Exception $e) {
            return $this->errorResponse(200, $e->getMessage());
        }
    }


    /**
     * Reset the given customer password.
     *
     * @param $customer
     * @param  string $password
     * @return void
     */
    protected function resetPassword($customer, $password)
    {

        $customer->password = Hash::make($password);
        $customer->setRememberToken(Str::random(60));
        $customer->save();
        event(new PasswordReset($customer));
    }

    /**
     * Get the broker to be used during password reset.
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker('customers');
    }

}