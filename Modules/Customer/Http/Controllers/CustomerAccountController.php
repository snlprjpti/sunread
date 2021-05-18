<?php

namespace Modules\Customer\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Repositories\CustomerAddressRepository;
use Modules\Customer\Repositories\CustomerRepository;
use Modules\Customer\Transformers\CustomerAccountResource;


class CustomerAccountController extends BaseController
{
    protected $repository, $customerAddressRepository;

    public function __construct(CustomerRepository $customerRepository, Customer $customer, CustomerAddressRepository $customerAddressRepository)
    {
        $this->repository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->model = $customer;
        $this->model_name = "Customer";
        $this->customer_id = auth()->guard("customer")->check() ? auth()->guard("customer")->user()->id : 0;

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CustomerAccountResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CustomerAccountResource($data);
    }

    public function show(): JsonResponse
    {
        try 
        {
            $fetched = auth()->guard("customer")->user();
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request): JsonResponse
    {
        try
        {
            $updated = auth()->guard("customer")->user();
            $merge = ["email" => "required|email|unique:customers,email,{$updated->id}",];

            if ( $request->has("current_password") ) {
                $current_password_validation = $request->has("password") ? "required" : "sometimes";
                $merge["current_password"] = "$current_password_validation|min:6|max:200";
            }

            $data = $this->repository->validateData($request, $merge);

            if ( $request->has("current_password") ) {
                if (!Hash::check($request->current_password, auth()->guard('customer')->user()->password)) {
                    throw new Exception("Password is incorrect.");
                }

                $data["password"] = Hash::make($request->password);
            }

            unset($data["current_password"]);
            $updated = $this->repository->update($data, $updated->id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

     public function uploadProfileImage(Request $request): JsonResponse
    {
        try
        {
            $updated = auth()->guard('customer')->user();
            // $this->repository->removeOldImage($updated->id);
            $updated = $this->repository->uploadProfileImage($request, $updated->id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), "Profile image updated successfully.");
    }

    public function deleteProfileImage(): JsonResponse
    {
        try
        {
            $updated = auth()->guard('customer')->user();
            $updated = $this->repository->removeOldImage($updated->id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), "Profile image deleted successfully.");
    }
}
