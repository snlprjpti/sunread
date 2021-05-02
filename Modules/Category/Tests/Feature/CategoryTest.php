<?php

namespace Modules\Category\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;

class CategoryTest extends BaseTestCase
{ 
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->model = Category::class;
        $this->model_name = "Category";
        $this->route_prefix = "admin.catalog.categories.categories";
        $this->default_resource_id = $this->model::latest()->first()->id;
        $this->fake_resource_id = 0;
        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getCreateData(): array
    {
        Storage::fake();
        $store = Store::factory()->create();

        return array_merge($this->model::factory()->make([
            "image" => UploadedFile::fake()->image("image.png")
        ])->toArray(), [
            "parent_id" => $this->model::oldest()->first()->id,
            "translation" => [
                "store_id" => $store->id,
                "name" => "Test"
            ]
        ]);
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "position" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null,
            "parent_id" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(),[
            "image" => null
        ]);   
    }
}
