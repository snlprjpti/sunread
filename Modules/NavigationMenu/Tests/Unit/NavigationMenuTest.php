<?php

namespace Modules\NavigationMenu\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Tests\BaseTestCase;
use Modules\NavigationMenu\Entities\NavigationMenu;

class NavigationMenuTest extends BaseTestCase
{
    protected $default_resource;

    public function setUp(): void
    {
        $this->model = NavigationMenu::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Navigation Menu";
        $this->route_prefix = "admin.navigation-menus";

        $this->model::factory(10)->create();

        $this->default_resource = $this->model::latest('id')->first();
        $this->default_resource_id = $this->default_resource->id;
        $this->hasStatusTest = true;
        $this->hasFilters = false;
    }
}
