<?php

namespace Modules\Core\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;

class StoreTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Store::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Store";
        $this->route_prefix = "admin.stores";
    }

    public function getCreateData(): array
    {
        Storage::fake();

        return $this->model::factory()->make([
            "image" => UploadedFile::fake()->image("image.png")
        ])->toArray();
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "image" => null
        ]);
    }
}
