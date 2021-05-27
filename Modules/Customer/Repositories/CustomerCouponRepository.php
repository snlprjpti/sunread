<?php

namespace Modules\Customer\Repositories;

use Carbon\Carbon;
use Modules\Coupon\Entities\Coupon;
use Exception;

class CustomerCouponRepository
{
    protected $model, $model_key;

    public function __construct(Coupon $coupon)
    {
        $this->model = $coupon;
        $this->model_key = "customers.coupons";
    }

    public function getPubliclyAvailableData($data): object
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
}
