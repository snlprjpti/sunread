<?php

namespace Modules\Core\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VisitorBaseTestCase extends TestCase
{
	use RefreshDatabase;

	public $model, $model_name, $route_prefix, $filter, $default_resource_id, $fake_resource_id, $factory_count, $append_to_route; 

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

		$this->createFactories = true;
        $this->hasFilters = true;
        $this->hasIndexTest = true;
        $this->hasShowTest = true;  
	}

	/*
	 * Factory methods 
	*/
	public function getCreateData(): array { return $this->model::factory()->make()->toArray(); }

	public function getRoute(string $method, ?array $parameters = null): string
    {
        $parameters = $this->append_to_route ? array_merge([$this->append_to_route], $parameters ?? []) : $parameters;
        return route("{$this->route_prefix}.{$method}", $parameters);
    }

	/*
	 * GET tests
	 * 1. Assert if resource list can be fetched
	 * 2. Assert if resource list can be fetched with filter
	 * 3. Assert if individual resouce can be fetched
	 * 4. Assert if application returns correct error for invalid resource id
	*/

	public function testVisitorCanFetchResource()
	{
        if ( !$this->hasIndexTest ) $this->markTestSkipped("Index method not available.");
        if ( $this->createFactories ) $this->model::factory($this->factory_count)->create();
		
		$response = $this->get($this->getRoute("index"));
		
		$response->assertOk();
		$response->assertJsonFragment([
			"status" => "success",
			"message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
		]);
	}

	public function testAdminCanFetchFilteredResources()
    {
        if ( !$this->hasIndexTest ) $this->markTestSkipped("Index method not available.");
        if ( !$this->hasFilters ) $this->markTestSkipped("Filters not available.");

        $response = $this->get($this->getRoute("index", $this->filter));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchIndividualResource()
    {
        if ( !$this->hasShowTest ) $this->markTestSkipped("Show method not available.");

        $response = $this->get($this->getRoute("show", [$this->default_resource_id]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        if ( !$this->hasShowTest ) $this->markTestSkipped("Show method not available.");

        $response = $this->get($this->getRoute("show", [$this->fake_resource_id]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}