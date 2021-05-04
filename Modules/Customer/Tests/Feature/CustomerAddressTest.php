<?php

namespace Modules\Customer\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Tests\BaseTestCase;
use Modules\Customer\Entities\CustomerAddress;
use Modules\User\Entities\Admin;
use Modules\User\Entities\Role;
use Tests\TestCase;

class CustomerAddressTest extends TestCase
{

    protected object $admin;
    protected array $headers;
    protected  int $customer_id;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = CustomerAddress::class;
        $this->model_name = "Customer Address";
        $this->route_prefix = "admin.customer.addresses";
        $this->default_resource_id = CustomerAddress::latest()->first()->id;
        $this->fake_resource_id = 0;
        $this->customer_id = 1;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function createAdmin(array $attributes = []): object
    {
        $password = $attributes["password"] ?? "password";
        $role_slug = $attributes["role_slug"] ?? "super-admin";
        $role = Role::where("slug", $role_slug)->firstOrFail();

        $data = [
            "password" => Hash::make($password),
            "role_id" => $role->id
        ];

        $admin = Admin::factory()->create($data);
        $token = $this->createToken($admin->email, $password);
        $this->headers["Authorization"] = "Bearer {$token}";

        return $admin;
    }

    public function createToken(string $admin_email, string $password): ?string
    {
        $jwtToken = Auth::guard("admin")
            ->setTTL( config("jwt.admin_jwt_ttl") )
            ->attempt([
                "email" => $admin_email,
                "password" => $password
            ]);
        return $jwtToken ?? null;
    }

    public function testAdminCanFetchResources()
    {
        $this->model::factory(10)->create();
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.index",$this->customer_id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success"
        ]);
    }


    public function testAdminCanFetchFilteredResources()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.index", $this->customer_id,$this->filter));

        $response->assertStatus(200);

    }

    public function testAdminCanFetchIndividualResource()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show", [$this->customer_id, $this->default_resource_id]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show", [$this->customer_id,$this->fake_resource_id]));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }

    public function getCreateData(): array { return $this->model::factory()->make()->toArray(); }

    public function testAdminCanCreateResource()
    {
        $post_data = $this->getCreateData();
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), [$this->customer_id,$post_data]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

}
