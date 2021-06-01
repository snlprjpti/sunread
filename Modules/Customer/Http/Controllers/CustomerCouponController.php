<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Transformers\CouponResource;
use Exception;
use Modules\Customer\Repositories\CustomerCouponRepository;

class CustomerCouponController extends BaseController
{
    private $repository;

    public function __construct(Coupon $coupon, CustomerCouponRepository $customerCouponRepository)
    {
        $this->model = $coupon;
        $this->model_name = "Customer Coupon";
        parent::__construct($this->model, $this->model_name);
        $this->repository = $customerCouponRepository;
    }
    public function collection(object $data): ResourceCollection
    {
        return CouponResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CouponResource($data);
    }

    public function publiclyAvailableCoupons(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $query = $this->model->publiclyAvailable();
            $fetched = $this->getFilteredList($request,[], $query);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $today = date('Y-m-d');
            $fetched = $this->model->whereId($id)->where('valid_from','<=',$today)->where('valid_to','>=',$today)->whereScopePublic(1)->whereStatus(1)->firstOrFail();
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang("fetch-success"));
    }

}
