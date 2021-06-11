<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Support\Arr;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageAvailability;
use Tests\TestCase;
use Illuminate\Support\Str;

class PageTest extends BaseTestCase
{
    protected int $parent_id;

    public function setUp(): void
    {
        $this->model = Page::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Page";
        $this->route_prefix = "admin.pages";

        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->parent_id = $this->model::oldest('id')->first()->id;
        $this->hasStatusTest = true;
    }

    public function getCreateData(): array
    {
        $store = Store::factory()->create();
        return array_merge($this->model::factory()->make()->toArray(), [
            "parent_id" => $this->parent_id,
            "translations" => [
                [
                    "store_id" => $store->id,
                    "title" => Str::random(10),
                    "description" => Str::random(30)
                ]
            ]
        ]);
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "position" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "title" => null
        ]);
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
        $model_type = Arr::random(config('model_list.model_types'));
        $post_data = [
            [
                "model_type" => $model_type,
                "model_id" => [random_int(1,10),random_int(1,10)],
                "status" => 1
            ]
        ];

        $response = $this->withHeaders($this->headers)->post($this->getRoute("allow_page", [$this->default_resource_id]), $post_data);
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

        $response->assertStatus(204);

        $check_resource = PageAvailability::whereIn("id", $resource_ids)->get()->count() > 0 ? true : false;
        $this->assertFalse($check_resource);
    }
}
