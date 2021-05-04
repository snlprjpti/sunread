<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Modules\Customer\Entities\Customer;
use Illuminate\Validation\ValidationException;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /**
     * Display a listing of the resource.
     * 
     * @param int $customer_id
     * @return JsonResponse
     */
    public function index($customer_id)
    {
        try
        {
            $fetched = Customer::with('addresses')->findOrFail($customer_id)->addresses;
        } 
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(CustomerAddressResource::collection($fetched), $this->lang('fetch-list-success'));
    }

    /**
     * Store a new resource
     *
     * @param Request $request
     * @param int $customer_id
     * @return JsonResponse
     */
    public function store(Request $request, $customer_id)
    {
        try
        {
            $customer = Customer::findOrFail($customer_id);
            $data = $this->repository->validateData($request);
            $data["customer_id"] = $customer->id;

            $created = $this->repository->create($data);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerAddressResource($created), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular resource
     * 
     * @param $customer_id
     * @param $address_id
     * @return JsonResponse
     */
    public function show($customer_id, $address_id)
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
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerAddressResource($fetched), $this->lang('fetch-success'));
    }


    /**
     * Update the specified resource.
     * 
     * @param Request $request
     * @param int $customer_id
     * @param int $address_id
     * @return JsonResponse
     */
    public function update(Request $request, $customer_id, $address_id)
    {
        try {
            Customer::with("addresses")
                ->findOrFail($customer_id)
                ->addresses()
                ->with("customer")
                ->where("id", $address_id)
                ->firstOrFail();

            $data = $this->repository->validateData($request, $address_id);
            $updated = $this->repository->update($data, $address_id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerAddressResource($updated), $this->lang('update-success'));
    }

    /**
     * Destroys the particular customer address
     * @param $customer_id
     * @param $address_id
     * @return JsonResponse
     */
    public function destroy($customer_id, $address_id)
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
        catch (CustomersPresentInGroup $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
