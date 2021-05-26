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
    public $model, $model_name, $route_prefix, $filter, $default_resource_id, $fake_resource_id, $factory_count, $append_to_route, $hasStatusRoute;

    public function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        $this->artisan("db:seed", ["--force" => true]);

        $this->factory_count = 2;
        $this->append_to_route = null;
        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->fake_resource_id = 0;
        $this->hasStatusRoute = false;
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

    public function getRoute(string $method, ?array $parameters = null): string
    {
        $parameters = $this->append_to_route ? array_merge([$this->append_to_route], $parameters ?? []) : $parameters;
        return route("{$this->route_prefix}.{$method}", $parameters);
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
        $response = $this->withHeaders($this->headers)->get($this->getRoute("index"));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchFilteredResources()
    {
        $response = $this->withHeaders($this->headers)->get($this->getRoute("index", $this->filter));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchIndividualResource()
    {
        $response = $this->withHeaders($this->headers)->get($this->getRoute("show", [$this->default_resource_id]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->get($this->getRoute("show", [$this->fake_resource_id]));

        $response->assertNotFound();
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
        $response = $this->withHeaders($this->headers)->post($this->getRoute("store"), $post_data);

        $response->assertCreated();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryCreateData();
        $response = $this->withHeaders($this->headers)->post($this->getRoute("store"), $post_data);

        $response->assertCreated();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }
    
    public function testShouldReturnErrorIfCreateDataIsInvalid()
    {
        $post_data = $this->getInvalidCreateData();
        $response = $this->withHeaders($this->headers)->post($this->getRoute("store"), $post_data);

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
        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryUpdateData();
        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }
    
    public function testShouldReturnErrorIfUpdateDataIsInvalid()
    {
        $post_data = $this->getInvalidUpdateData();
        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfUpdateResourceDoesNotExist()
    {
        $post_data = $this->getUpdateData();
        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->fake_resource_id]), $post_data);

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }

    /**
     * DELETE tests
     * 
     * 1. Assert if resource can be deleted
     * 2. Assert if application returns correct error when trying to delete non-existent data
     */
    
    public function testAdminCanDeleteResource()
    {
        $resource_id = $this->model::factory()->create()->id;
        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$resource_id]));

        $response->assertNoContent();

        $check_resource = $this->model::whereId($resource_id)->first() ? true : false;
        $this->assertFalse($check_resource);
    }

    public function testShouldReturnErrorIfDeleteResourceDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$this->fake_resource_id]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }

    /**
     * STATUS tests
     * 
     * 1. Assert if reource's status can be updated
     * 2. Assert if application returns correct error when trying to update non-existent data
     */

    public function testAdminCanUpdateResourceStatus()
    {
        if ( !$this->hasStatusRoute ) $this->markTestSkipped("Status update method not available.");

        $resource = $this->model::factory()->create(["status" => 1]);
        $response = $this->withHeaders($this->headers)->put($this->getRoute("status", [$resource->id]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.status-updated", ["name" => $this->model_name])
        ]);

        // Checking if status has been updated
        $expected_resource_status = !$resource->status;
        $updated_resource_status = $this->model::find($resource->id)->status;
        $this->assertTrue($updated_resource_status === $expected_resource_status);
    }

    public function testShouldReturnErrorIfUpdateStatusResourceDoesNotExist()
    {
        if ( !$this->hasStatusRoute ) $this->markTestSkipped("Status update method not available.");

        $response = $this->withHeaders($this->headers)->put($this->getRoute("status", [$this->fake_resource_id]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}
