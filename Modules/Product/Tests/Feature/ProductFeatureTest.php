<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Support\Str;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Product\Entities\Feature;

class ProductFeatureTest extends BaseTestCase
{
    public $default_resource;

    public function setUp(): void
    {
        $this->model = Feature::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Feature";
        $this->route_prefix = "admin.catalog.features";
        $this->default_resource = $this->model::latest('id')->first();
        $this->default_resource_id = $this->default_resource->id;
    }

    public function getCreateData(): array
    {
        $translations = [
            "translations" => [
                [
                    "store_id" => Store::factory()->create()->id,
                    "name" => Str::random(10),
                    "description" => Str::random(20)
                ]
            ]
        ];
        return array_merge($this->model::factory()->make()->toArray(), $translations);
    }

    public function getUpdateData(): array
    {
        $translations = [
            "translations" => [
                [
                    "store_id" => Store::factory()->create()->id,
                    "name" => Str::random(10),
                    "description" => Str::random(20)
                ]
            ]
        ];
        return  array_merge($this->model::factory()->make()->toArray(), $translations);
    }

    public function getNonMandatoryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "description" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null
        ]);
    }
}
