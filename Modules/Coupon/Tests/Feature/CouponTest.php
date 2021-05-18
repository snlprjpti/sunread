<?php

namespace Modules\Coupon\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Coupon\Entities\Coupon;

class CouponTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Coupon::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Coupon";
        $this->route_prefix = "admin.coupons";
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null,
            "status"=>null
        ]);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "discount_percent" => null
        ]);
    }

    public function testAdminCanAllowCoupon()
    {
        $post_data = [
            "model_type" => 'dsadasd',
            "model_id" => 1,
            "status" => 1
        ];

        $response = $this->withHeaders($this->headers)->post($this->getRoute("allow_coupon", [$this->default_resource_id]), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
        ]);
    }
}
