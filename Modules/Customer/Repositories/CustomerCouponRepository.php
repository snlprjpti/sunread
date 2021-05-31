<?php

namespace Modules\Customer\Repositories;

use Carbon\Carbon;
use Modules\Coupon\Entities\AllowCoupon;
use Modules\Coupon\Entities\Coupon;
use Exception;
use Modules\Customer\Exceptions\CouponAlreadyApplyException;

class CustomerCouponRepository
{
    protected $model, $model_key, $allowCoupon;

    public function __construct(Coupon $coupon, AllowCoupon $allowCoupon)
    {
        $this->model = $coupon;
        $this->model_key = "customers.coupons";
        $this->allowCoupon = $allowCoupon;
    }

    public function getPubliclyAvailableCouponDetail(int $id): object
    {
        try
        {
            $today = Carbon::today()->format('Y-m-d');
            $fetched = $this->model->whereId($id)->where('valid_from','<=',$today)->where('valid_to','>=',$today)->whereScopePublic(1)->whereStatus(1)->firstOrFail();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $fetched;
    }

}
