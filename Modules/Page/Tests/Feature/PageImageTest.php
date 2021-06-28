<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageImage;

class PageImageTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = PageImage::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Page Image";
        $this->route_prefix = "admin.pages.images";

        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasUpdateTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function getCreateData(): array
    {
        Storage::fake();

        $page_id = Page::factory()->create()->id;
        return $this->model::factory()->make([
            "page_id" => $page_id,
            "image" => [
                UploadedFile::fake()->image('image.jpeg'),
                UploadedFile::fake()->image('image-2.jpeg')
            ]
        ])->toArray();
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "page_id" => null,
            "image" => null,
        ]);
    }
}
