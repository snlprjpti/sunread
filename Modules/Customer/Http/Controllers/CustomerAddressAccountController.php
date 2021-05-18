<?php

namespace Modules\Customer\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Customer\Repositories\CustomerAddressRepository;
use Modules\Customer\Transformers\CustomerAddressResource;

class CustomerAddressAccountController extends BaseController
{
    protected $repository, $customer;

    public function __construct(CustomerAddressRepository $customerAddressRepository, CustomerAddress $customerAddress)
    {
        $this->repository = $customerAddressRepository;
        $this->model = $customerAddress;
        $this->model_name = "Customer Address";
        $this->customer = auth()->guard("customer")->check() ? auth()->guard("customer")->user() : [];
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

    public function show(): JsonResponse
    {
        try
        {
            $fetched = $this->customer->addresses;
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-success"));
    }

    public function create(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);
            $data["customer_id"] = $this->customer->id;
            $created = $this->repository->create($data);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $this->checkAuthority($id);
            $data = $this->repository->validateData($request);
            $updated = $this->repository->update($data, $id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        try
        {
            $this->checkAuthority($id);
            $this->repository->delete($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
    
        return $this->successResponseWithMessage($this->lang("delete-success"), 204);
    }

    protected function checkAuthority($id): void
    {
        $customer_address_ids = $this->customer->addresses->pluck("id")->toArray() ?? [];
        if (!in_array($id, $customer_address_ids)) throw new Exception("Action not authorized.");
    }

    protected function checkDefaultAddress():void
    {

    }
}
