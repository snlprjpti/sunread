<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Modules\Customer\Entities\Customer;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Exceptions\TokenGenerationException;
use Modules\Customer\Exceptions\CustomerNotFoundException;

/**
 * Reset Password controller for the Customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ResetPasswordController extends BaseController
{
    public function __construct(Customer $customer)
    {
        $this->model = $customer;
        $this->model_name = "Password Reset";

        parent::__construct($this->model, $this->model_name);
    }

    /** OPTIONAL route
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
            : $this->errorResponse("Missing token", 400);
    }

    /**
     * Store new password of the admin
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                "token" => "required",
                "email" => "required|email|exists:customers,email",
                "password" => "required|confirmed|min:6",
                "password_confirmation" => "required"
            ]);

            $response = $this->broker()->reset($data, function ($customer, $password) {
                $this->resetPassword($customer, $password);
            });

            if ($response == Password::INVALID_TOKEN) throw new TokenGenerationException($this->lang("token-generation-problem", []));
            if ($response == Password::INVALID_USER) throw new CustomerNotFoundException("Customer not found exception");
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (CustomerNotFoundException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 404);
        }
        catch (TokenGenerationException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang("create-success"));
    }

    /**
     * Get the broker to be used during password reset.
     * 
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
