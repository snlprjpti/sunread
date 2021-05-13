<?php
namespace Modules\Coupon\Database\factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Coupon\Entities\Coupon;

class CouponFactory extends Factory
{
    protected $model = \Modules\Coupon\Entities\Coupon::class;

    public function definition()
    {
        $date = $this->faker->date();
        return [
            "code" => $this->faker->unique()->slug(),
            "name" => $this->faker->name(),
            "description" => $this->faker->paragraph(),
            "valid_from" => $date,
            "valid_to" => Carbon::parse(now())->format('Y.m.d'),
            "flat_discount_amount" => random_int(1,100),
            "min_discount_amount" => random_int(1,100),
            "max_discount_amount" => random_int(1,500),
            "min_purchase_amount" => random_int(1,1000),
            "discount_percent" => 1,
            "max_uses" => 10,
            "single_user_uses" => 1,
            "only_new_user" => 0,
            "scope_public" => 0,
            "status" => 1
        ];
    }
}

