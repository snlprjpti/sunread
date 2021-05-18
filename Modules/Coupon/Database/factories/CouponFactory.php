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
            "flat_discount_amount" => rand(1,100),
            "min_discount_amount" => rand(1,100),
            "max_discount_amount" => rand(1,500),
            "min_purchase_amount" => rand(1,1000),
            "discount_percent" => rand(0, 10),
            "max_uses" => rand(1,1000),
            "single_user_uses" => rand(1, 10),
            "only_new_user" => rand(0, 1),
            "scope_public" => rand(0, 1),
            "status" => rand(0, 1)
        ];
    }
}

