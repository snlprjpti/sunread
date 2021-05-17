<?php

namespace Modules\Coupon\Repositories;


use Modules\Core\Repositories\BaseRepository;
use Modules\Coupon\Entities\AllowCoupon;
use Exception;

class AllowCouponRepository extends BaseRepository
{
    public function __construct(AllowCoupon $allowCoupon)
    {
        $this->model = $allowCoupon;
        $this->model_key = "coupon allow";
        $this->rules = [
            "coupon_id" => "required",
            "model_type" => "required",
            "model_id" => "required",
            "status" => "required|boolean"
        ];
    }

    public function allowedCouponExist($request)
    {
        try
        {
            $exists = $this->model->where('coupon_id',$request->coupon_id)
                ->where('model_id',$request->model_id)
                ->where('model_type',$request->model_type)
                ->where('status',$request->status)
                ->count();
            return $exists;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
