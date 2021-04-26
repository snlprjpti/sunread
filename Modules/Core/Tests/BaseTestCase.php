<?php

namespace Modules\Core\Tests;

use Tests\TestCase;
use Modules\User\Entities\Role;
use Modules\User\Entities\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseTestCase extends TestCase
{
	use RefreshDatabase;

	protected array $headers;
    public $model, $model_name, $route_prefix, $filter, $default_resource_id, $fake_resource_id;

	public function setUp(): void
	{
		parent::setUp();
		Schema::disableForeignKeyConstraints();
		$this->artisan("db:seed", ["--force" => true]);
	}

    /**
     * Fake methods
     */
    public function getCreateData(): array { return []; }
    public function getNonMandodtaryCreateData(): array { return $this->getCreateData(); }
    public function getInvalidCreateData(): array { return $this->getCreateData(); }

    public function getUpdateData(): array { return $this->getUpdateData(); }
    public function getNonMandodtaryUpdateData(): array { return $this->getUpdateData(); }
    public function getInvalidUpdateData(): array { return $this->getUpdateData(); }

    /**
     * Generate Admin data
     */
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

    /**
     * GET tests
     * 
     * 1. Assert if resource list can be fetched
     * 2. Assert if resource list can be fetched with filter
     * 3. Assert if individual resource can be fetched
     * 4. Assert if application returns correct error for invalid resource id
     */

    public function testAdminCanFetchResources()
    {
        $this->model::factory(10)->create();
        $response = $this->get(route("{$this->route_prefix}.index"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchFilteredResources()
    {
        $response = $this->get(route("{$this->route_prefix}.index", $this->filter));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchIndividualResource()
    {
        $response = $this->get(route("{$this->route_prefix}.show", $this->default_resource_id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        $response = $this->get(route("{$this->route_prefix}.show", $this->fake_resource_id));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }

    /**
     * POST tests
     * 
     * 1. Assert if resource can be created
     * 2. Assert if resource can be created with non mandatory data
     * 3. Assert if application returns correct error for invalid data
     */

    public function testAdminCanCreateResource()
    {
        $post_data = $this->getCreateData();
        $response = $this->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "payload" => $post_data,
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryCreateData();
        $response = $this->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }
    
    public function testShouldReturnErrorIfCreateDataIsInvalid()
    {
        $post_data = $this->getInvalidCreateData();
        $response = $this->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    /**
     * PUT tests
     * 
     * 1. Assert if resource can be updated
     * 2. Assert if resource can be updated with non mandatory data
     * 3. Assert if application returns correct error for invalid data
     * 4. Assert if application returns correct error when trying to update non-existent data
     */
    public function testAdminCanUpdateResource()
    {
        $post_data = $this->getUpdateData();
        $response = $this->put(route("{$this->route_prefix}.update", $this->default_resource_id), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "payload" => $post_data,
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryUpdateData();
        $response = $this->put(route("{$this->route_prefix}.update", $this->default_resource_id), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }
    
    public function testShouldReturnErrorIfUpdateDataIsInvalid()
    {
        $post_data = $this->getInvalidUpdateData();
        $response = $this->put(route("{$this->route_prefix}.update", $this->default_resource_id), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfUpdateResourceDoesNotExist()
    {
        $post_data = $this->getUpdateData();
        $response = $this->put(route("{$this->route_prefix}.update", $this->fake_resource_id), $post_data);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }

    /**
     * DELETE tests
     * 
     * 1. Assert if resource can be deleted
     * 2. Assert if application returns correct error when trying to delet non-existent data
     */
    
    public function testAdminCanDeleteResource()
    {
        $resource_id = $this->model::factory()->create()->id;
        $response = $this->delete(route("{$this->route_prefix}.destroy", $resource_id));

        $response->assertStatus(204);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.deleted-success", ["name" => $this->model_name])
        ]);

        $check_resource = $this->model::whereId($resource_id)->first() ? true : false;
        $this->assertFalse($check_resource);
    }

    public function testShouldReturnErrorIfDeleteResourceDoesNotExist()
    {
        $response = $this->delete(route("{$this->route_prefix}.destroy", $this->fake_resource_id));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}