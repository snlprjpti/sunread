<?php

namespace Modules\Attribute\Tests\Feature;

use Modules\Attribute\Entities\AttributeGroup;
use Modules\Core\Tests\BaseTestCase;

class AttributeGroupTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->model = AttributeGroup::class;
        $this->model_name = "Attribute Group";
        $this->route_prefix = "admin.catalog.attribute-groups";

        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
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
