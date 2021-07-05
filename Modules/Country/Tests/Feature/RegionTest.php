<?php

namespace Modules\Country\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Country\Entities\Region;

class RegionTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Region::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Region";
        $this->route_prefix = "admin.regions";

        $this->createFactories = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
    }
}
