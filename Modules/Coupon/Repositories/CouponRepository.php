<?php


namespace Modules\Coupon\Repositories;


use Modules\Coupon\Entities\Coupon;

class CouponRepository
{
    public function __construct(Coupon $coupon)
    {
        $this->model = $coupon;
        $this->model_key = "coupons";
        $this->rules = [
            "code" => "required|unique:products,sku",
            "name" => "required",
            "description" => "sometimes|nullable",
            "valid_from" => "required",
            "valid_to" => "required",
            "flat_discount_amount" => "sometimes|nullable",
            "min_discount_amount" => "sometimes|nullable",
            "max_discount_amount" => "sometimes|nullable",
            "min_purchase_amount" => "sometimes|nullable",
            "discount_percent" => "sometimes|nullable",
            "max_uses" => "required",
            "single_user_uses" => "sometimes|nullable",
            "only_new_user" => "sometimes|boolean",
            "scope_public" => "sometimes|boolean",
            "status" => "sometimes|boolean"
        ];
    }

}
