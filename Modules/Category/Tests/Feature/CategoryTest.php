<?php

namespace Modules\Category\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Illuminate\Support\Str;
use Modules\Core\Entities\Website;

class CategoryTest extends BaseTestCase
{
    protected int $root_category_id;

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
        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->root_category_id = $this->model::oldest('id')->first()->id;
        $this->hasStatusTest = true;
    }

    public function getCreateData(): array
    {
        Storage::fake();
        $store = Store::factory()->create();


        return array_merge($this->model::factory()->make()->toArray(), [
            "attributes" => [
                [
                    "name" => [
                        "value" => Str::random(10),
                    ],
                    "image" => [
                        "value" => UploadedFile::fake()->image("image.png")
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

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "position" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null,
            "parent_id" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(),[
            "image" => null
        ]);
    }
}
