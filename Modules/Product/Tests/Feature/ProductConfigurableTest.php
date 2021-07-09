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

    public function getUpdateData(): array
    {
        $product = $this->model::find($this->default_resource_id);
        $attribute = $product->attribute_set->attribute_groups->first()->attributes->first();

        return $this->model::factory()->make([
            "attributes" => [
                [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute->type)
                ]
            ],
            "super_attributes" => [
                "size" => ["L", "XL"],
                "color" => ["red", "blue"]
            ]
        ])->toArray();
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return $this->getUpdateData();
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "attribute_set_id" => null,
            "sku" => null
        ]);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "brand_id" => null
        ]);
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
                $value = \Str::random(10);
                break;
        }
        return $value;
    }


}
