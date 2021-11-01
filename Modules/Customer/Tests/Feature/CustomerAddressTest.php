<?php

namespace Modules\Customer\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = CustomerAddress::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Customer Address";
        $this->route_prefix = "admin.customers.addresses";
        $this->append_to_route = Customer::latest("id")->first()->id;
    }

    public function getNonMandatoryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "address2" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "address1" => null
        ]);
    }

    public function testAdminCanUpdateResourceWithDefaultAddress()
    {
        $post_data = [
            "default_shipping_address" => 1,
            "default_billing_address" => 1
        ];
        $response = $this->withHeaders($this->headers)->put($this->getRoute("defaultAddress", [$this->default_resource_id]), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchIndividualDefaultAddresses()
    {
        $response = $this->withHeaders($this->headers)->get($this->getRoute("default"));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }
}
