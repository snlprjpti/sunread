<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Modules\Core\Tests\BaseTestCase;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\ProductImage;
use Modules\Core\Entities\Website;

class ProductTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Product::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product";
        $this->route_prefix = "admin.catalog.products";
        $this->hasStatusTest = true;
    }

    public function getCreateData(): array
    {
        $category = Category::inRandomOrder()->first();
        $product = $this->model::factory()->make();
        $attribute = $product->attribute_set->attribute_groups->first()->attributes->first();        
        return array_merge([
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute->type)
                ]
            ],
            "categories" => [$category->id]
        ], $product->toArray());
    }

    public function getUpdateData(): array
    {
        $category = Category::inRandomOrder()->first();
        $product = $this->model::find($this->default_resource_id);
        $update_data = $product->toArray();
        $attribute = $product->attribute_set->attribute_groups->first()->attributes->first();

        return array_merge([
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute->type)
                ]
            ],
            "categories" => [$category->id]
        ], $product->toArray());
    }

    public function getNonMandodtaryUpdateData(): array
    {
        $category = Category::inRandomOrder()->first();
        $product = $this->model::find($this->default_resource_id);
        $update_data = $product->toArray();
        $attribute = $product->attribute_set->attribute_groups->first()->attributes->first();

        return array_merge([
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute->type)
                ]
            ],
            "categories" => [$category->id]
        ], $product->toArray());
    }

    public function value(string $type): mixed
    {
        switch($type)
        {
            case "price" : 
                $value = 1000.1;
                break;

            case "boolean" : 
                $value = true;
                break;

            case ($type == "datetime" || $type == "date"):
                $value = now();
                break;

            case "number":
                $value = rand(1,1000);
                break;

            default:
                $value = Str::random(10);
                break;
        }
        return $value;
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

    /**
     * Product Image Tests
     */

    public function testAdminCanUploadProductImage()
    {
        Storage::fake();

        $post_data = [
            "product_id" => $this->default_resource_id,
            "image" => [
                UploadedFile::fake()->image('image.jpeg'),
                UploadedFile::fake()->image('image-2.jpeg')
            ]
        ];

        $response = $this->withHeaders($this->headers)->post($this->getRoute("image.store"), $post_data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => "Product Image"])
        ]);
    }

    public function testAdminShouldBeAbleToChangeMainProductImage()
    {
        $product_image = ProductImage::factory(3)->create()->first();
        $product_image_id = $product_image->id;
        $expected_main_image = !$product_image->main_image;

        $response = $this->withHeaders($this->headers)->put($this->getRoute("image.change_main_image", [$product_image_id]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.status-change-success", ["name" => "Product Image"])
        ]);

        // Check if main_image has been changed as expected
        $product_image = ProductImage::whereId($product_image_id)->first();
        $this->assertEquals($product_image->main_image, $expected_main_image);

        // CHeck if other images of the products has been changed to 0
        $other_images = ProductImage::whereProductId($product_image->product_id)
            ->whereMainImage(1)
            ->where("id", "<>", $product_image->id)
            ->get();
        $this->assertEquals($other_images->count(), 0);
    }

    public function testAdminShouldBeAbleToDeleteProductImage()
    {
        $product_image_id = ProductImage::factory()->create()->id;
        $response = $this->withHeaders($this->headers)->delete($this->getRoute("image.destroy", [$product_image_id]));

        $response->assertOk();
    }

    public function testShouldReturnErrorIfDeleteProductImageDoesNotExist()
    {
        $response = $this->withHeaders($this->headers)->delete($this->getRoute("image.destroy", [$this->fake_resource_id]));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found", ["name" => "Product Image"])
        ]);
    }
}
