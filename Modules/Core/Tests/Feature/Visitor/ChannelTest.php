<?php

namespace Modules\Core\Tests\Feature\Visitor;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Channel;
use Modules\Core\Tests\VisitorBaseTestCase;

class ChannelTest extends VisitorBaseTestCase
{
    public function setUp(): void
    {
        $this->model = Channel::class;

        parent::setUp();
        $this->model_name = "Channel";
        $this->route_prefix = "channels";
        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;  
    }
    
    public function getCreateData(): array
    {
        Storage::fake();

        return $this->model::factory()->make([
            "logo" => UploadedFile::fake()->image("logo.png"),
            "favicon" => UploadedFile::fake()->image("favicon.png")
        ])->toArray();
    }

    public function testVisitorCanFetchChannelStoresResource()
    {
        $response = $this->getCreateData();
        $response = $this->get($this->getRoute("stores.index", [$this->default_resource_id]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-list-success", ["name" => $this->model_name])
        ]);
    }
}
