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
        $this->model = Attribute::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Attribute";
        $this->route_prefix = "admin.catalog.attributes";
    }

    public function getCreateData(): array
    {
        return array_merge($this->model::factory()->make([
            "is_user_defined" => 1
        ])->toArray(), [
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

    public function testShouldReturnErrorIfNonUserDefinedAttributeIsDeleted()
    {
        $resource_id = $this->model::factory()->create([
            "is_user_defined" => 0
        ])->id;

        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$resource_id]));

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }
}
