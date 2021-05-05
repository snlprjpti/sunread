<?php

namespace Modules\Brand\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Brand\Entities\Brand;
use Modules\Core\Tests\BaseTestCase;

class BrandTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->model = Brand::class;
        $this->model_name = "Brand";
        $this->route_prefix = "admin.brands";
        $this->default_resource_id = Brand::latest()->first()->id;
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
