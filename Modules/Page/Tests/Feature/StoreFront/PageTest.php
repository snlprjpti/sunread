<?php

namespace Modules\Page\Tests\Feature\StoreFront;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Core\Entities\Store;
use Modules\Page\Entities\Page;
use Tests\TestCase;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    public $model, $model_name, $route_prefix, $default_resource_slug, $append_to_route, $store_code;

    public function setUp(): void
    {
        $this->model = Page::class;

        parent::setUp();

        $this->model_name = "Page";
        $this->route_prefix = "public.pages";

        $this->default_resource_slug = $this->model::latest('id')->first()->slug;
        $this->store_code = Store::oldest("id")->first()->code;
    }

    public function testUserShouldBeAbleToGetPage()
    {
        $this->headers["store"] = "{$this->store_code}";
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show", [$this->default_resource_slug]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success",  ["name" => $this->model_name])
        ]);
    }

    public function testUserShouldNotBeAbleToGetPageWithInvalidStoreCode()
    {
        $this->headers["store"] = "Invalid_Code";
        $response = $this->withHeaders($this->headers)->get(route("{$this->route_prefix}.show", [$this->default_resource_slug]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}
