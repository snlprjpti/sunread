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
use Exception;

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

    public function index(int $customer_id): JsonResponse
    {
        try
        {
            $fetched = Customer::with('addresses')->findOrFail($customer_id)->addresses;
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request, int $customer_id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, $this->repository->regionAndCityValidation($request), function () use($customer_id) {
                return [
                    "customer_id" => Customer::findOrFail($customer_id)->id
                ];
            });

            $created = $this->repository->create($data, function($created) use($data) {
                $this->repository->unsetOtherAddresses($created, $data);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $customer_id, int $address_id): JsonResponse
    {
        try
        {
            $fetched = Customer::with("addresses")
                ->findOrFail($customer_id)
                ->addresses()
                ->findOrFail($address_id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $customer_id, int $address_id): JsonResponse
    {
        try {

            $data = $this->repository->validateData($request, $this->repository->regionAndCityValidation($request), function () use($customer_id) {
                return [
                    "customer_id" => $customer_id
                ];
            });

            $updated = $this->repository->update($data, $address_id, function($updated) use($data) {
                $this->repository->unsetOtherAddresses($updated, $data);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $customer_id, int $address_id): JsonResponse
    {
        try
        {
            $deleted = Customer::with("addresses")
                ->findOrFail($customer_id)
                ->addresses()
                ->findOrFail($address_id);

            $deleted->delete();
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    public function updateAddress(Request $request, int $customer_id, int $address_id)
    {
        try
        {
            $data = $request->validate([
                "default_billing_address" => "required_without:default_shipping_address|boolean",
                "default_shipping_address" => "required_without:default_billing_address|boolean",
            ]);

            $customer = Customer::with("addresses")->findOrFail($customer_id);

            $fetched = $customer->addresses()->findOrFail($address_id);
            $fetched->fill($data);
            $fetched->save();
            
            if(isset($data["default_billing_address"]) && $data["default_billing_address"] == 1) $customer->addresses()->where("id", "<>", $address_id)->update([
                "default_billing_address" => 0
            ]);

            if(isset($data["default_shipping_address"]) && $data["default_shipping_address"] == 1) $customer->addresses()->where("id", "<>", $address_id)->update([
                "default_shipping_address" => 0
            ]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang('update-success'));
    }
}
