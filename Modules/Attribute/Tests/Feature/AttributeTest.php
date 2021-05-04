<?php

namespace Modules\Attribute\Tests\Feature;

use Illuminate\Support\Str;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Attribute\Entities\Attribute;

class AttributeTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->model = Attribute::class;
        $this->model_name = "Attribute";
        $this->route_prefix = "admin.catalog.attributes";

        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getCreateData(): array
    {
        return array_merge($this->model::factory()->make()->toArray(), [
            "translations" => [
                [
                    "store_id" => Store::factory()->create()->id,
                    "name" => Str::random(10)
                ]
            ]
        ]);
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "slug" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null
        ]);
    }
}
