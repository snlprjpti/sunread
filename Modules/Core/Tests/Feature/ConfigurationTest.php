<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Configuration;
use Modules\User\Entities\Admin;
use Modules\User\Entities\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfigurationTest extends TestCase
{
    use RefreshDatabase;
    protected object $admin;
    protected array $headers;
    public $model, $model_name, $route_prefix, $default_resource;

    public function setUp(): void
    {
        parent::setUp();
		Schema::disableForeignKeyConstraints();
		$this->artisan("db:seed", ["--force" => true]);

        $this->admin = $this->createAdmin();

        $this->model = Configuration::class;
        $this->model_name = "Configuration";
        $this->route_prefix = "admin.configurations";
        $this->default_resource = Configuration::latest()->first();
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
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.index"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanCreateResources()
    {
        $post_data = $this->model::factory()->make()->toArray();
        $response =  $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfScopeFieldIsInvalid()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "scope" => "invalid"
        ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfScopeIdFieldIsInvalid()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "scope_id" => 0
        ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldUpdateDataIfExists()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "scope" => $this->default_resource->scope,
            "scope_id" => $this->default_resource->scope_id,
            "path" => $this->default_resource->path,
            ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "value" => null
        ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResourceWithNonMandatoryData()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "scope" => $this->default_resource->scope,
            "scope_id" => $this->default_resource->scope_id,
            "path" => $this->default_resource->path,
            "value" => null
            ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }
}
