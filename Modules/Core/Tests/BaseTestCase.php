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
    public $model, $model_name, $route_prefix, $filter, $default_resource_id, $fake_resource_id, $factory_count;

    public function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        $this->artisan("db:seed", ["--force" => true]);

        $this->factory_count = 2;
        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->fake_resource_id = 0;
        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    /**
     * Fake methods
     */
    public function getCreateData(): array { return $this->model::factory()->make()->toArray(); }
    public function getNonMandodtaryCreateData(): array { return $this->getCreateData(); }
    public function getInvalidCreateData(): array { return $this->getCreateData(); }

    public function getUpdateData(): array { return $this->getCreateData(); }
    public function getNonMandodtaryUpdateData(): array { return $this->getNonMandodtaryCreateData(); }
    public function getInvalidUpdateData(): array { return $this->getInvalidCreateData(); }

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
        $this->model::factory($this->factory_count)->create();
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.index"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchFilteredResources()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.index", $this->filter));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchIndividualResource()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show", $this->default_resource_id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show", $this->fake_resource_id));

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
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryCreateData();
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }
    
    public function testShouldReturnErrorIfCreateDataIsInvalid()
    {
        $post_data = $this->getInvalidCreateData();
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

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
        $response = $this->withHeaders($this->headers)->put(route("{$this->route_prefix}.update", $this->default_resource_id), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryUpdateData();
        $response = $this->withHeaders($this->headers)->put(route("{$this->route_prefix}.update", $this->default_resource_id), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }
    
    public function testShouldReturnErrorIfUpdateDataIsInvalid()
    {
        $post_data = $this->getInvalidUpdateData();
        $response = $this->withHeaders($this->headers)->put(route("{$this->route_prefix}.update", $this->default_resource_id), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfUpdateResourceDoesNotExist()
    {
        $post_data = $this->getUpdateData();
        $response = $this->withHeaders($this->headers)->put(route("{$this->route_prefix}.update", $this->fake_resource_id), $post_data);

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
        $response = $this->withHeaders($this->headers)->delete(route("{$this->route_prefix}.destroy", $resource_id));

        $response->assertStatus(204);

        $check_resource = $this->model::whereId($resource_id)->first() ? true : false;
        $this->assertFalse($check_resource);
    }

    public function testShouldReturnErrorIfDeleteResourceDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->delete(route("{$this->route_prefix}.destroy", $this->fake_resource_id));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}
