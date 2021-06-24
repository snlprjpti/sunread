<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Support\Arr;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
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
        $this->hasShowTest = false;
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

    public function testAdminCanFetchIndividualResource()
    {
        $scope = Arr::random(config('page.model_config'));
        $scope_id = app($scope)::factory(1)->create()->first()->id;
        $page_id = Page::factory(1)->create()->first()->id;
        $get_data = [
            "page_id" => $page_id,
            "scope" => $scope,
            "scope_id" => $scope_id
        ];
        $response = $this->withHeaders($this->headers)->get($this->getRoute("detail", $get_data));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testShouldReturnErrorIfResourceDoesNotExist()
    {
        $scope = Arr::random(config('page.model_config'));
        $scope_id = app($scope)::factory(1)->create()->first()->id;
        $get_data = [
            "page_id" => 0,
            "scope" => $scope,
            "scope_id" => $scope_id
        ];
        $response = $this->withHeaders($this->headers)->get($this->getRoute("detail", $get_data));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => $this->model_name])
        ]);
    }
}
