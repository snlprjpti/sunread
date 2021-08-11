<?php

namespace Modules\Page\Tests\Feature\StoreFront;

use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;

class PageTest extends BaseTestCase
{
    private $store_code;

    public function setUp(): void
    {
        $this->model = Page::class;

        parent::setUp();

        $this->model_name = "Page";
        $this->route_prefix = "pages";

        $this->default_resource_slug = $this->model::latest('id')->first()->slug;
        $this->store_code = Store::oldest("id")->first()->code;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function testUserShouldBeAbleToGetPage()
    {
        $this->headers["store_code"] = "{$this->store_code}";
        $response = $this->withHeaders($this->headers)->get($this->getRoute("show", [$this->default_resource_slug]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success",  ["name" => $this->model_name])
        ]);
    }

    public function testUserShouldNotBeAbleToGetPageWithInvalidStoreCode()
    {
        $this->headers["store_code"] = "Invalid_Code";
        $response = $this->withHeaders($this->headers)->get($this->getRoute("show", [$this->default_resource_slug]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}
