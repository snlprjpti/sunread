<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;

/**
 * Reset Password controller for the Admin
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
                request(['email', 'password', 'password_confirmation', 'token']), function ($admin, $password) {
                $this->resetPassword($admin, $password);
            }
            );
            if ($response == Password::PASSWORD_RESET) {
                return $this->successResponse(200, null, trans('core::app.users.users.password-reset-success'));
            }
            return $this->errorResponse(400, "Invalid token");

        }catch (ValidationException $exception){
            return $this->errorResponse(400, $exception->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(400, $e->getMessage());
        }
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

    /**
     * Get the broker to be used during password reset.
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker('admins');
    }

}