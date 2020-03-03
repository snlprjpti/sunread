<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Exceptions\CustomerNotFoundException;
use Modules\Customer\Exceptions\TokenGenerationException;


/**
 * Reset Password controller for the Customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ResetPasswordController extends BaseController
{
    use ResetsPasswords;

    /** OPTIONAL route
     * Display the password reset token is valid
     * If no token is present, display the error
     * @param  string|null $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($token = null)
    {
        if (!$token) {
            return $this->errorResponse("Missing token", 400);
        }
        return $this->successResponse($payload = ['token' => $token]);
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
                $request->only(['email', 'password', 'password_confirmation', 'token']), function ($customer, $password) {
                $this->resetPassword($customer, $password);
            });


            if ($response == Password::INVALID_TOKEN) {
                throw  new TokenGenerationException();
            }

            if ($response == Password::INVALID_USER) {
                throw  new CustomerNotFoundException();
            }

            return $this->successResponseWithMessage(trans('core::app.response.create-success', ['name' => 'Password reset ']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (CustomerNotFoundException $exception) {
            return $this->errorResponse('Customer not found exception', 404);

        } catch (TokenGenerationException $exception) {
            return $this->errorResponse(trans('core::app.users.token.token-generation-problem'));

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get the broker to be used during password reset.
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker('customers');
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

}