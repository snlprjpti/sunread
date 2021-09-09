<?php

namespace Modules\Customer\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Customer\Repositories\CustomerAddressRepository;

class CustomerAddressAccountController extends BaseController
{
    protected $repository, $customer;

    public function __construct(CustomerAddressRepository $customerAddressRepository, CustomerAddress $customerAddress)
    {
        $this->repository = $customerAddressRepository;
        $this->model = $customerAddress;
        $this->model_name = "Customer Address";
        $this->customer = auth()->guard("customer")->user();
        parent::__construct($this->model, $this->model_name);
    }

    public function show(): JsonResponse
    {
        try
        {
            $fetched["shipping"] = $this->repository->checkShippingAddress($this->customer->id)->first();
            $fetched["billing"] =$this->repository->checkBillingAddress($this->customer->id)->first();
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang("fetch-success"));
    }

    public function create(Request $request): JsonResponse
    {
        try
        {
            $created = $this->repository->createOrUpdate($request, $this->customer->id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($created, $this->lang("create-success"));
    }
}
