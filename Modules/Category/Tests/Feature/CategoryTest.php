<?php

namespace Modules\Category\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Illuminate\Support\Str;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;

class CategoryTest extends BaseTestCase
{
    protected int $root_category_id;
    protected $default_resource;

    public function setUp(): void
    {
        $this->model = Category::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Category";
        $this->route_prefix = "admin.catalog.categories";

        $this->filter = [
            "website_id" => Website::inRandomOrder()->first()->id
        ];

        $this->model::factory(10)->create();
        $this->default_resource = $this->model::latest('id')->first();
        $this->default_resource_id = $this->default_resource->id;
        $this->root_category_id = $this->model::oldest('id')->first()->id;
        $this->hasStatusTest = true;
        $this->hasIndexTest = false;
        $this->hasFilters = false;
        $this->hasShowTest = false;
        $this->hasStatusTest = false;
    }

    public function getCreateData(): array
    {
        return array_merge($this->model::factory()->make()->toArray(), [
            "attributes" => [
                [
                    "name" => [
                        "value" => Str::random(10),
                    ],
                    "description" => [
                        "value" => Str::random(20),
                    ],
                    "meta_title" => [
                        "value" => Str::random(11),
                    ],
                    "meta_description" => [
                        "value" => Str::random(15),
                    ],
                    "meta_keywords" => [
                        "value" => Str::random(13),
                    ],
                    "status" => [
                        "value" => rand(0,1)
                    ],
                    "include_in_menu" => [
                        "value" => rand(0,1)
                    ]
                ]
            ]
        ]);
    }

    public function getUpdateData(): array
    {
        return array_merge($this->getCreateData(), [
            "scope" => "website",
            "scope_id" => $this->default_resource->website_id,
            "website_id" => $this->default_resource->website_id
        ]); 
    }

    public function testAdminCanFetchResources()
    {
        if ( $this->createFactories ) $this->model::factory($this->factory_count)->create();

        $response = $this->withHeaders($this->headers)->get($this->getRoute("index"));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanFetchFilteredResources()
    {
        $this->markTestSkipped("Index method not available.");
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "parent_id" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "website_id" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(),[
            "parent_id" => null
        ]);
    }
}
