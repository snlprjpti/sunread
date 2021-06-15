<?php

namespace Modules\Attribute\Tests\Feature;

use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Tests\BaseTestCase;
use Illuminate\Support\Str;

class AttributeSetTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = AttributeSet::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Attribute Set";
        $this->route_prefix = "admin.attribute.sets";
        $this->hasStatusTest = true;
    }

    public function getCreateData(): array
    {
        return $this->model::factory()->make([
            "groups" => [
                [
                    "name" => Str::random(10),
                    "position" => 1,
                    "attributes" => Attribute::whereIsUserDefined(0)->whereIsRequired(0)->pluck('id')->toArray()
                ]
            ]
        ])->toArray();
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
