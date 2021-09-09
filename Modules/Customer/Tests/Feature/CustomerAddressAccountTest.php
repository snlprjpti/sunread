<?php

namespace Modules\Customer\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressAccountTest extends TestCase
{
    use DatabaseTransactions;

    protected object $customer, $fake_customer;
    protected array $headers;

    public $model, $route_prefix, $model_name, $type;

    public function setUp(): void
    {
        $this->model = CustomerAddress::class;
        parent::setUp();

        $this->customer = $this->createCustomer();
        $this->model_name = "Customer Address";
        $this->route_prefix = "customers.address";
    }

    public function getCreateData(): array
    {
        return $this->model::factory()->make([
            "customer_id" => $this->customer->id
        ])->toArray();
    }

    public function createCustomer(array $attributes = []): object
    {
        $password = $attributes["password"] ?? "password";

        $data = [
            "password" => Hash::make($password),
        ];

        $customer = Customer::factory()->create($data);
        $token = $this->createToken($customer->email, $password);
        $this->headers["Authorization"] = "Bearer {$token}";

        return $customer;
    }

    public function createToken(string $customer_email, string $password): ?string
    {
        $jwtToken = Auth::guard("customer")
            ->setTTL( config("jwt.customer_jwt_ttl") )
            ->attempt([
                "email" => $customer_email,
                "password" => $password
            ]);
        return $jwtToken ?? null;
    }

    public function testCustomerCanAddOwnAddress()
    {
        $post_data["shipping"] = $this->getCreateData();
        $post_data["billing"] = $this->getCreateData();
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.create"), $post_data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testCustomerCanFetchOwnAddresses()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }
}
