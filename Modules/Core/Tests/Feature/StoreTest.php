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
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->model = Store::class;
        $this->model_name = "Store";
        $this->route_prefix = "admin.stores";
        $this->default_resource_id = Store::latest()->first()->id;
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
            "image" => UploadedFile::fake()->image("image.png")
        ])->toArray();
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(),[]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(),["name" => null]);
    }

    public function getUpdateData(): array
    {
        Storage::fake();
        return $this->model::factory()->make([
            "image" => UploadedFile::fake()->image("image.png")
        ])->toArray();
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(),["image" => null]);
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(),["name" => null]);
    }


}
