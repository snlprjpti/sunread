<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Customer\Entities\Customer;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Transformers\CustomerResource;
use Modules\Customer\Repositories\CustomerRepository;

/**
 * Registration Controller customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class RegistrationController extends BaseController
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
     * Register a customer
     * 
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        try
        {
            $data = $this->repository->validateData($request);
            $created = $this->repository->create($data);

        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerResource($created), $this->lang('create-success'), 201);
    }
}
