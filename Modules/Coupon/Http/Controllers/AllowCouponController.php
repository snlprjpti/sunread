<?php

namespace Modules\Coupon\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Coupon\Entities\AllowCoupon;
use Exception;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Repositories\AllowCouponRepository;
use Modules\Coupon\Transformers\AllowCouponResource;

class AllowCouponController extends BaseController
{
    private $coupon;
    private $repository;

    public function __construct(AllowCoupon $allowCoupon, Coupon $coupon, AllowCouponRepository $allowCouponRepository)
    {
        $this->model = $allowCoupon;
        $this->model_name = "Coupon Allow";
        $this->coupon = $coupon;
        $this->repository = $allowCouponRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return AllowCouponResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new AllowCouponResource($data);
    }

    public function allowCoupon(Request $request, int $couponId): JsonResponse
    {
        try
        {
            $coupon = $this->coupon->where('id',$couponId)->where('status',1)->first();
            if($coupon){
                $request['coupon_id'] = $couponId;
                $allowExist = $this->repository->allowedCouponExist($request);
                if ($allowExist < 0) {
                    $data = $this->repository->validateData($request);
                    $created = $this->repository->create($data);
                }
                else{
                    return $this->successResponseWithMessage($this->lang('already-created'));
                }
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }
}
