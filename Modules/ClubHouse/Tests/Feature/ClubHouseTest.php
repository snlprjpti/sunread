<?php

namespace Modules\ClubHouse\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\ClubHouse\Entities\ClubHouse;

class ClubHouseTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = ClubHouse::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Club House";
        $this->route_prefix = "admin.clubhouses";
    }

    public function testAdminCanFetchResources()
    {
        $response = $this->withHeaders($this->headers)->get($this->getRoute("index"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }
}
