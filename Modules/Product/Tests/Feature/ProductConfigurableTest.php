<?php

namespace Modules\Product\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;

class ProductConfigurableTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Product::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product";
        $this->route_prefix = "admin.catalog.configurable-products";
        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function getCreateData(): array
    {
        return $this->model::factory()->make()->toArray();
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

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "sku" => null
        ]);
    }


}
