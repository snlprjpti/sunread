<?php

namespace Modules\Country\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Country\Entities\City;

class CityTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = City::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "City";
        $this->route_prefix = "admin.cities";

        $this->createFactories = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
    }
}
