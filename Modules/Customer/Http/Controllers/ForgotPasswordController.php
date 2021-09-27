<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Modules\Customer\Entities\Customer;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Exceptions\CustomerNotFoundException;
use Modules\Customer\Repositories\CustomerRepository;
use Modules\Customer\Exceptions\TokenGenerationException;
use Exception;

class ForgotPasswordController extends BaseController
{
    public function __construct( Customer $customer)
    {
        $this->model = $customer;
        $this->model_name = "Customer";
        $exception_statuses = [
            TokenGenerationException::class => 401,
            CustomerNotFoundException::class => 404
        ];
        parent::__construct($this->model, $this->model_name,$exception_statuses);
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $this->validate($request, ["email" => "required|email"]);

            $customer = $this->model::where("email", $request->email)->firstOrFail();
            $response = $this->broker()->sendResetLink(["email" => $customer->email]);

            if ($response == Password::INVALID_TOKEN) throw new TokenGenerationException(__("core::app.users.token.token-generation-problem"));
            if ($response == Password::INVALID_USER) throw new CustomerNotFoundException("Customer not found.");
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        Event::dispatch( "storefront.customer.forgot.password", [ "customer_id" => $customer->id] );

        return $this->successResponseWithMessage("Reset Link sent to your email {$customer->email}");
    }

    public function broker(): PasswordBroker
    {
        return Password::broker('customers');
    }
}
