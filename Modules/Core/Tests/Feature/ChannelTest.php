<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Channel;
use Modules\Core\Tests\BaseTestCase;

class ChannelTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = Channel::class;
        $this->model_name = "Channel";
        $this->route_prefix = "admin.channels";
        $this->default_resource_id = Channel::latest()->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getCreateData(): array
    {
        Storage::fake();

        return $this->model::factory()->make([
            "logo" => UploadedFile::fake()->image("logo.png"),
            "favicon" => UploadedFile::fake()->image("favicon.png")
        ])->toArray();
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), []);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "code" => null
        ]);
    }

    public function getUpdateData(): array
    {
        Storage::fake();

        return $this->model::factory()->make([
            "logo" => UploadedFile::fake()->image("logo.png"),
            "favicon" => UploadedFile::fake()->image("favicon.png")
        ])->toArray();
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "logo" => null,
            "favicon" => null
        ]);
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "code" => null
        ]);
    }
}
