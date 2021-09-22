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
        parent::__construct($this->model, $this->model_name);
    }

    public function show(Request $request): JsonResponse
    {
        try
        {
            $channel_code = $request->header("hc-channel");
            $fetched["shipping"] = null;
            $fetched["billing"] = null;
            if($channel_code) {
                $website = Website::findOrFail($this->customer->website_id);
                $channel_id = ($channel = $website->channels->where("code", $channel_code)->where("website_id", $website->id)->first()) ? $channel->id : null;
                if($channel_id) {
                    $fetched["shipping"] = $this->repository->checkShippingAddress($this->customer->id, $channel_id)->first();
                    $fetched["billing"] =$this->repository->checkBillingAddress($this->customer->id, $channel_id)->first();
                }
            }
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
