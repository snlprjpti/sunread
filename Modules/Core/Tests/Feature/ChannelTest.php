<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Channel;
use Modules\Core\Tests\BaseTestCase;

class ChannelTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Channel::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Channel";
        $this->route_prefix = "admin.channels";
    }

    public function getCreateData(): array
    {
        Storage::fake();

        return $this->model::factory()->make([
            "logo" => UploadedFile::fake()->image("logo.png"),
            "favicon" => UploadedFile::fake()->image("favicon.png")
        ])->toArray();
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "code" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "logo" => null,
            "favicon" => null
        ]);
    }
}
