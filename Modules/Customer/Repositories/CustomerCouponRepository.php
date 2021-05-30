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

    public function getPubliclyAvailableData(object $data): object
    {
        try
        {
            $today = Carbon::today()->format('Y-m-d');
            $fetched = $data->where('valid_from','<=',$today)->where('valid_to','>=',$today)->where('status',1)->where('scope_public',1);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $fetched;
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


    public function getAvailableCoupon(object $data): object
    {
        try
        {
            $today = Carbon::today()->format('Y-m-d');
            $fetched = $this->model->whereStatus(1)
                ->whereCode($data->coupon_code)
                ->where('valid_from','<=',$today)
                ->where('valid_to','>=',$today)
                ->where('min_purchase_amount','<=',$data->total_amount)
                ->firstOrFail();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $fetched;
    }

    public function applyCoupon(object $coupon): object
    {
        try
        {
//            for eg
            $user_id = 1;
            $data['coupon_id'] = $coupon->id;
            $data['model_type'] = '\Modules\Customer\Entities\Customer';
            $data['model_id'] = $user_id;
            $data['status'] = 0;
            $allow_check = $this->alreadyApplied($data);
            if ($allow_check){
                throw new CouponAlreadyApplyException();
            }
            $created = $this->allowCoupon->create($data);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $created;
    }

    public function alreadyApplied(array $data): bool
    {
        try
        {
            $exists = $this->allowCoupon->where($data)->exists();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $exists;
    }
}
