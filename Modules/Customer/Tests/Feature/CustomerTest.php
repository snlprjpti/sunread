<?php

namespace Modules\Customer\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Customer\Entities\Customer;

class CustomerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Customer::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Customer";
        $this->route_prefix = "admin.customers";
    }

    public function getCreateData(): array
    {
        return array_merge($this->model::factory()->make()->toArray(), [
            "password" => "password",
            "gender" => "male",
            "password_confirmation" => "password"
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "email" => null
        ]);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
<<<<<<< HEAD
            "customer_group_id" => null
        ]);
    }

    public function testAdminCanUpdateResourceStatus()
    {
        $resource = $this->model::factory()->create(["status" => 1]);
        $response = $this->withHeaders($this->headers)->put($this->getRoute("deactivate", [$resource->id]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.status-updated", ["name" => $this->model_name])
        ]);

        // Checking if status has been updated
        $expected_resource_status = !$resource->status;
        $updated_resource_status = $this->model::find($resource->id)->status;
        $this->assertTrue($updated_resource_status == $expected_resource_status);
    }

    public function testShouldReturnErrorIfUpdateStatusResourceDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->put($this->getRoute("deactivate", [$this->fake_resource_id]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
=======
            "last_name" => null,
            "gender" => null,
            "date_of_birth" => null,
            "customer_group_id" => null,
            "remember_token" => null
        ]);
    }

>>>>>>> 5fdfc1b (refactor customer fixed)
}
