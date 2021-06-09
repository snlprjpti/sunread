<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Core\Entities\Store;
use Illuminate\Http\UploadedFile;
use Modules\Core\Entities\Channel;
use Modules\Core\Tests\BaseTestCase;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\ProductImage;
use Modules\Product\Entities\ProductAttribute;

class ProductAttributeTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = ProductAttribute::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product Attribute";
        $this->route_prefix = "admin.catalog.products.attributes";

        $this->createFactories = false;
        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasStoreTest = true;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $this->markTestSkipped("All data are mandatory.");
    }
    
    public function getCreateData(): array
    {
        $scope = Arr::random(["store", "channel", ""]);
        $scope_array = [];
        if ( $scope !== "" ) {
            $scope_array = [
                "scope" => $scope,
                "scope_id" => $scope == "store" ? Store::inRandomOrder()->first()->id : Channel::inRandomOrder()->first()->id
            ];
        }

        $product_attribute = $this->model::factory()->make()->toArray();

        return array_merge(
            $scope_array,
            [
                "product_id" => Product::inRandomOrder()->first()->id,
                "attributes" => [
                    [
                        "attribute_id" => $product_attribute["attribute_id"],
                        "value" => $product_attribute["value_data"]
                    ]
                ]
            ]
        );
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "product_id" => null
        ]);
    }
}
