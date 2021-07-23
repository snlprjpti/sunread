<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Modules\Core\Entities\Website;
use Modules\Core\Tests\BaseTestCase;

class ResolverTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Website::class;

        parent::setUp();

        $this->model_name = "Website";
        $this->route_prefix = "resolver";
        
        $this->createFactories = false;
        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
    }

    public function testWebsiteCanBeResolved(): void
    {
        $response = $this->get($this->getRoute("resolve"));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testCustomWebsiteCanBeResolved(): void
    {
        $response = $this->get($this->getRoute("resolve", Website::inRandomOrder()->first()->host_name));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testCustomWebsiteShouldNotBeResolvedIfInvalid(): void
    {
        $response = $this->get($this->getRoute("resolve", ["random-non-existent.domain"]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
    
    public function testWebsiteShouldNotBeResolvedIfInProduction(): void
    {
        Config::set("website.environment", "production");
        $response = $this->get($this->getRoute("resolve"));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}
