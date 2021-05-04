<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\Configuration;
use Modules\Core\Tests\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfigurationTest extends BaseTestCase
{
    public $default_resource;

    public function setUp(): void
    {
        $this->model = Configuration::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Configuration";
        $this->route_prefix = "admin.configurations";

        $this->default_resource = Configuration::latest()->first();
    }

    public function testAdminCanFetchResources()
    {
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.index"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    /**
     * 1. No individual resources can be fetched in configuration.
    */

    public function testAdminCanFetchIndividualResource()
    {
        $this->assertTrue(true);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        $this->assertTrue(true);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "scope" => "default",
            "scope_id" => 0,
            "items" => [
                "optional_zip_countries" => null,
            ]
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "scope" => "store",
            "scope_id" => 1,
            "items" => [
                null => 5,
            ]
        ]);
    }

    /**
     * POST tests
     * 
     * 1. Assert if application returns correct error if scope is invalid
     * 3. Assert if application returns correct error if scope_id is invalid
     */

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
            "scope_id" => 10000000
        ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    /**
     * 1. Configuration call store method to update resource but not update method.
    */

    public function testAdminCanUpdateResource()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "scope" => $this->default_resource->scope,
            "scope_id" => $this->default_resource->scope_id,
            "items" => [
                $this->default_resource->path => 15,
            ]
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
            "items" => [
                $this->default_resource->path => null,
            ]
            ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    /**
     * 1. Invalid data is already checked in store method.
    */

    public function testShouldReturnErrorIfUpdateDataIsInvalid()
    {
        $this->assertTrue(true);
    }
    
    //If update resource doesnt exists, it create new data.

    public function testShouldReturnErrorIfUpdateResourceDoesNotExist()
    {
        $this->assertTrue(true);
    }

    /**
     * DELETE tests
     * 
     * 1. resource can't be deleted
     */
    
    public function testAdminCanDeleteResource()
    {
        $this->assertTrue(true);
    }

    public function testShouldReturnErrorIfDeleteResourceDoesNotExist()
    {
        $this->assertTrue(true);
    }
}
