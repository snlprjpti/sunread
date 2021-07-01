<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Support\Arr;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageAvailability;

class AllowPageTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Page::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Page Availability";
        $this->route_prefix = "admin.pages";

        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $this->markTestSkipped("All data are mandatory.");
    }

    public function testAdminCanFetchModelListResources()
    {
        $response = $this->withHeaders($this->headers)->get($this->getRoute("model_list"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name"=>"Model List"])
        ]);
    }

    public function testAdminCanAllowPage()
    {
        $model_type = Arr::random(config('page.model_list'));
        $resource_ids = app($model_type)::factory(2)->create()->pluck("id")->toArray();
        $post_data = [
            [
                "model_type" => $model_type,
                "model_id" => $resource_ids,
                "status" => 1
            ]
        ];

        $response = $this->withHeaders($this->headers)->put($this->getRoute("allow_page", [$this->default_resource_id]), $post_data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => "Page Availability"])
        ]);
    }

    public function testAdminCanDeleteAllowPage()
    {
        $resource_ids = PageAvailability::factory(2)->create()->pluck("id")->toArray();

        $response = $this->withHeaders($this->headers)->delete($this->getRoute("delete_allow_page"),[
            "ids" => $resource_ids
        ]);

        $response->assertOk();

        $check_resource = PageAvailability::whereIn("id", $resource_ids)->get()->count() > 0 ? true : false;
        $this->assertFalse($check_resource);
    }
}
