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
        $this->model = Page::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Page Images";
        $this->route_prefix = "admin.pages.images";

        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasStoreTest = false;
        $this->hasShowTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function testAdminCanUploadPageImage()
    {
        Storage::fake();

        $post_data = [
            "page_id" => $this->default_resource_id,
            "image" => [
                UploadedFile::fake()->image('image.jpeg'),
                UploadedFile::fake()->image('image-2.jpeg')
            ]
        ];

        $response = $this->withHeaders($this->headers)->post($this->getRoute("store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => "Page Image"])
        ]);
    }

    public function testAdminShouldBeAbleToDeletePageImage()
    {
        $product_image_id = PageImage::factory()->create()->id;
        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$product_image_id]));

        $response->assertStatus(204);
    }

    public function testShouldReturnErrorIfDeletePageImageDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$this->fake_resource_id]));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => "Page Image"])
        ]);
    }
}
