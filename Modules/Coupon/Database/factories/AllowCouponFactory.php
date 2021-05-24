<?php
namespace Modules\Coupon\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Coupon\Entities\Coupon;

class AllowCouponFactory extends Factory
{
    protected $model = \Modules\Coupon\Entities\AllowCoupon::class;

    public function definition(): array
    {
        $model_type = Arr::random(["\Modules\Customer\Entities\Customer", "\Modules\Brand\Entities\Brand", "\Modules\Product\Entities\Product"]);
        return [
            "coupon_id" => Coupon::latest()->first()->id,
            "model_type" => $model_type,
            "model_id" => rand(1, 10),
            "status" => 1
        ];
    }
}

