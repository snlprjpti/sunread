<?php

namespace Modules\Attribute\Tests\Feature;

use Modules\Attribute\Entities\AttributeFamily;
use Modules\Core\Tests\BaseTestCase;

class AttributeFamilyTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = AttributeFamily::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Attribute Family";
        $this->route_prefix = "admin.catalog.families";
        $this->hasStatusTest = true;
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
