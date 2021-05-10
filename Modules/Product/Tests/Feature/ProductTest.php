<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Modules\Core\Tests\BaseTestCase;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\ProductImage;

class ProductTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Product::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product";
        $this->route_prefix = "admin.catalog.products";
    }

    public function getCreateData(): array
    {
        $attribute = Attribute::whereType("text")->inRandomOrder()->first();
        $category = Category::inRandomOrder()->first();

        return $this->model::factory()->make([
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => Str::random(10)
                ]
            ],
            "categories" => [$category->id]
        ])->toArray();
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "parent_id" => null,
            "brand_id" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "sku" => null
        ]);
    }

    public function testAdminCanUploadProductImage()
    {
        Storage::fake();
        $post_data = [
            "product_id" => Product::first()->id,
            "image" => UploadedFile::fake()->image('image.jpeg')
        ];

        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.image.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success"
        ]);
    }


    public function testAdminShouldBeAbleToDeleteProductImage()
    {
        $product_image_id = ProductImage::factory()->create()->id;
        $response = $this->withHeaders($this->headers)->delete(route("{$this->route_prefix}.image.destroy",$product_image_id));
        $response->assertStatus(204);
    }
}
