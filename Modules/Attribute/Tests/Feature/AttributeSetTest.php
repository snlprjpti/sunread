<?php

namespace Modules\Attribute\Tests\Feature;

use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Tests\BaseTestCase;

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
