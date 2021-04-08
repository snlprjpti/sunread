<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Entities\Customer;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Repositories\CustomerRepository;
use Modules\Customer\Exceptions\TokenGenerationException;

/**
 * Forgot Password controller for the Customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ForgotPasswordController extends BaseController
{
    protected $repository;

    public function __construct(CustomerRepository $customerRepository, Customer $customer)
    {
        $this->repository = $customerRepository;
        $this->model = $customer;
        $this->model_name = "Account";

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
        try {
            $data = $request->validate([
                "email" => "required|email|exists:customers,email"
            ]);

            $response = $this->broker()->sendResetLink($data);
            if ($response != Password::RESET_LINK_SENT) throw new TokenGenerationException($this->lang("token-generation-problem", []));
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);

        }catch(TokenGenerationException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage("Reset Link sent to your email {$request->email}");
    }

    /**
     * Get the broker to be used during password reset
     * 
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('customers');
    }
}
