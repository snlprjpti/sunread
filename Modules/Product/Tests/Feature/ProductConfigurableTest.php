<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Support\Arr;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Tests\BaseTestCase;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;

class ProductConfigurableTest extends BaseTestCase
{
    public $default_resource;
    public function setUp(): void
    {
        $this->model = Product::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product";
        $this->route_prefix = "admin.catalog.configurable-products";
        $this->default_resource = $this->model::latest('id')->first();
        $this->default_resource_id = $this->default_resource->id;
        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function getCreateData(): array
    {
        $product = $this->model::factory()->make();
        $merge_product = $product->toArray(); 

        $attributes = [];

        foreach ( $product->attribute_set->attribute_groups as $attribute_group )
        {
            foreach ($attribute_group->attributes as $attribute)
            {
                if (in_array($attribute->slug, ["category_ids", "base_image", "small_image", "thumbnail_image", "quantity_and_stock_status"])) continue;
                $attributes[] = [
                    "attribute_id" => $attribute->id,
                    "value" => $this->value($attribute->type)
                ];
            }
        }

        $super_attributes = Attribute::inRandomOrder()->whereisUserDefined(0)->whereType("select")->where("slug", "!=", "tax_class_id")->take(2)->get();
      
        foreach($super_attributes as $super_attribute)
        {
            $variant_attributes[] = [
                "attribute_id" => $super_attribute->id,
                "value" => $super_attribute->attribute_options->take(2)->pluck('id')->toArray()
            ];
        }
        return array_merge($merge_product, ["attributes" => $attributes], ["super_attributes" => $variant_attributes]);
    }

    public function getUpdateData(): array
    {
        $websiteId = $this->default_resource->website_id;
        $updateData = $this->getCreateData();
        return array_merge($updateData, $this->getScope($websiteId)); 
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
            "attribute_set_id" => null
        ]);
    }

    public function testShouldReturnErrorIfUpdateDataIsInvalid()
    {
        $this->markTestSkipped("Invalid Update method not available.");
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

    public function getScope($websiteId)
    {
        $scope = Arr::random([ "website", "channel", "store" ]);
        $channels = Website::find($websiteId)->channels;
        if(count($channels) > 0 ){
            switch($scope)
            {
                case "website":
                    $scope_id = $websiteId;
                    break; 
    
                case "channel":
                    $scope_id = $channels->first()->id;
                    break;
    
                case "store":
                    $stores = $channels->first()->stores;
                    $scope_id = (count($stores) > 0) ? $stores->first()->id : $this->getScope("channel", $websiteId);
                    break;
            }
        }
        return [
            "scope" => isset($scope_id) ? $scope : "website",
            "scope_id" => isset($scope_id) ? $scope_id : $websiteId
        ];

    }


}
