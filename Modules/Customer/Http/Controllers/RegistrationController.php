<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Facades\SiteConfig;
use Modules\Customer\Entities\Customer;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Transformers\CustomerResource;
use Modules\Customer\Repositories\StoreFront\CustomerRepository;
use Exception;
use Modules\Notification\Events\NewAccount;
use Modules\Notification\Events\RegistrationSuccess;

class RegistrationController extends BaseController
{
    protected $repository;

    public function __construct(CustomerRepository $customerRepository, Customer $customer)
    {
        $this->repository = $customerRepository;
        $this->model = $customer;
        $this->model_name = "Customer Registration";

        parent::__construct($this->model, $this->model_name);
    }

    public function resource(object $data): JsonResource
    {
        return new CustomerResource($data);
    }

    public function register(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->registration($request);
            $created = $this->repository->create($data);

        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        event(new RegistrationSuccess($created->id));
        if(SiteConfig::fetch("require_email_confirmation", "website", $request->website_id) == 1) {
            event(new NewAccount($created->id, $created->verification_token));
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }
}
