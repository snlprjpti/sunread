<?php

namespace Modules\Customer\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Website;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Customer\Repositories\StoreFront\AddressRepository;

class CustomerAddressAccountController extends BaseController
{
    protected $repository, $customer;

    public function __construct(AddressRepository $addressRepository, CustomerAddress $customerAddress)
    {
        $this->repository = $addressRepository;
        $this->model = $customerAddress;
        $this->model_name = "Customer Address";
        $this->customer = auth()->guard("customer")->user();
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');
        parent::__construct($this->model, $this->model_name);
    }

    public function show(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->getCustomerAddress($request, $this->customer);
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
