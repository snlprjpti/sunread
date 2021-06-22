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
    protected $attribute_group, $attribute_set_id;

    public function setUp(): void
    {
        $this->model = Product::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product";
        $this->route_prefix = "admin.catalog.products";
        $this->hasStatusTest = true;

        $this->attribute_set_id = AttributeSet::factory()->create()->id;
        $this->attribute_group = AttributeGroup::factory(1)
        ->create(["attribute_set_id" => $this->attribute_set_id])
        ->each(function ($attr_group){
            $attr_group->attributes()->attach(Attribute::factory(1)->create());
        })->first();

        // $this->hasUpdateTest = false;

    }

    public function getCreateData(): array
    {
        $category = Category::inRandomOrder()->first();
        $attribute = $this->attribute_group->attributes[0];
        
        return $this->model::factory()->make([
            "website_id" => Website::factory()->create()->id,
            "attribute_set_id" => $this->attribute_group->attribute_set_id,
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute)
                ]
            ],
            "categories" => [$category->id]
        ])->toArray();
    }

    public function testAdminCanUpdateResourceWithNonMandatoryData()
    {
        // $product = $this->model::find($this->default_resource_id);
        
        // $attribute = $product->attribute_set->attribute_groups[0]->attributes[0];

        // $category = Category::inRandomOrder()->first();
        // $post_data = $this->model::factory()->make([
        //     "website_id" => Website::factory()->create()->id,
        //     "attribute_set_id" => $product->attribute_set_id,
        //     "attributes" => [
        //         [
        //             "attribute_id" => $attribute->id,
        //             "value" => $this->value($attribute)
        //         ]
        //     ],
        //     "categories" => [$category->id]
        // ])->toArray();

        $post_data = $this->getUpdateData();

        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResource()
    {
        $product = $this->model::find($this->default_resource_id);
        $attribute = $product->attribute_set->attribute_groups[0]->attributes[0];

        $category = Category::inRandomOrder()->first();
        $post_data = $this->model::factory()->make([
            "website_id" => Website::factory()->create()->id,
            "attribute_set_id" => $product->attribute_set_id,
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute)
                ]
            ],
            "categories" => [$category->id]
        ])->toArray();

        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function value(object $attribute): mixed
    {
        switch($attribute->type)
        {
            case "price" : 
                $value = 1000.1;
                break;

            case "boolean" : 
                $value = 1;
                break;

            case "timestamp" : 
                $value = now();
                break;

            default:
                $value = Str::random(10);
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

        $response->assertStatus(204);
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
