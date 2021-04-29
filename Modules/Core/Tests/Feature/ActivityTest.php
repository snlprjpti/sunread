<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\ActivityLog;
use Modules\Core\Tests\BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = ActivityLog::class;
        $this->model_name = "ActivityLog";
        $this->route_prefix = "admin.activities";
        $this->default_resource_id = ActivityLog::latest()->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function testFetchActivityList()
    {
        $response = $this->get(route("{$this->route_prefix}.index"));

        $response->assertStatus(200);
        $response->assertJson([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }

    public function testFetchActivity()
    {
        $response = $this->get(route("{$this->route_prefix}.show", $this->default_resource_id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }


}
