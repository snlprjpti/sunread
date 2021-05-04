<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Transformers\CustomerAddressResource;
use Modules\Customer\Repositories\CustomerAddressRepository;

class AddressController extends BaseController
{
    protected $repository;

    public function __construct(CustomerAddressRepository $customerAddressRepository, CustomerAddress $customer_address)
    {
        $this->repository = $customerAddressRepository;
        $this->model = $customer_address;
        $this->model_name = "Customer Address";

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CustomerAddressResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CustomerAddressResource($data);
    }

    public function index($customer_id): JsonResponse
    {
        try
        {
            $fetched = Customer::with('addresses')->findOrFail($customer_id)->addresses;
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request, int $customer_id): JsonResponse
    {
        try
        {
            $customer = Customer::findOrFail($customer_id);
            $data = $this->repository->validateData($request);
            $data["customer_id"] = $customer->id;
            $created = $this->repository->create($data);
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show($customer_id, $address_id): JsonResponse
    {
        try
        {
            $fetched = Customer::with("addresses")
                ->findOrFail($customer_id)
                ->addresses()
                ->with("customer")
                ->where("id", $address_id)
                ->firstOrFail();
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, $customer_id, $address_id): JsonResponse
    {
        try {
            Customer::with("addresses")
                ->findOrFail($customer_id)
                ->addresses()
                ->with("customer")
                ->where("id", $address_id)
                ->firstOrFail();

            $data = $this->repository->validateData($request);
            $updated = $this->repository->update($data, $address_id);
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy($customer_id, $address_id): JsonResponse
    {
        try
        {
            $deleted = Customer::with("addresses")
                ->findOrFail($customer_id)
                ->addresses()
                ->with("customer")
                ->where("id", $address_id)
                ->firstOrFail();

            $deleted->delete();
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'),204);
    }
}
