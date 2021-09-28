<?php

namespace Modules\Product\Traits\ElasticSearch;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Website;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Product\Entities\AttributeConfigurableProduct;
use Modules\Product\Entities\AttributeOptionsChildProduct;
use Modules\Product\Entities\Product;

trait ConfigurableProductHandler
{
    use HasIndexing;

    public function createProduct(object $parent, object $store): void
    {
        try
        {
            $items = [];
            $this->bulkConfigurableRemoving($parent, $store);

            $variants = $parent->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();
            
            $variant_attribute_options = $variants->map(function($variant) {
                return $variant->attribute_options_child_products->pluck("attribute_option_id", "product_id")->toArray();
            })->flatten(1)->unique();

            if ($this->checkVisibility($parent, $store)) {
                $product_format = $parent->documentDataStructure($store); 
                $final_parent = array_merge($product_format, $this->getAttributeData($variant_attribute_options, $parent));
                if(count($final_parent) > 0) $this->configurableIndexing($final_parent, $store);   
            }
            foreach($variants as $variant)
            {
                if (!$this->checkVisibility($variant, $store)) continue;
                
                $product_format = $variant->documentDataStructure($store); 

                $group_by_attribute = AttributeConfigurableProduct::whereProductId($parent->id)->whereUsedInGrouping(1)->first();
                $is_group_attribute = $variant->value([
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $group_by_attribute->attribute_id
                ]);

                $related_variants = AttributeOptionsChildProduct::whereIn("product_id", $variants->pluck("id")->toArray())->whereAttributeOptionId($is_group_attribute?->id)->get();
                if($related_variants) {
                    $variant_attribute_options = AttributeOptionsChildProduct::whereIn("product_id", $related_variants->pluck("product_id")->toArray())->where("attribute_option_id", "!=", $is_group_attribute?->id)->get()->pluck("attribute_option_id", "product_id");
                }

                $final_variant = array_merge($product_format, $this->getAttributeData($variant_attribute_options, $variant));  
                if(count($final_variant) > 0) $this->configurableIndexing($final_variant, $store);          
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function getAttributeData(object $variant_options, object $product): array
    {
        try
        {
            $items = [];
            $variant_options->map(function($variant_option, $key) use(&$items, $product) {
                $attribute_option = AttributeOption::find($variant_option);
                $attribute = $attribute_option->attribute;
                
                $items["configurable_{$attribute->slug}"][] = $variant_option;
                $items["configurable_{$attribute->slug}_value"][] = $attribute_option->name;

                if($product->parent_id ) {
                    $catalog = CatalogInventory::whereProductId($key)->first();
                    $items["configurable"][$attribute->slug][] = [
                        "label" => $attribute_option->name,
                        "value" => $variant_option,
                        "stock_status" => ($catalog?->is_in_stock && $catalog?->quantity > 0) ? 1 : 0
                    ];
                    $items["type"] = "configurable";
                    $items["config_attribute_status"] = 1;
                }
                else {
                    $items["config_attribute_status"] = 0;
                }
                if(isset($items[$attribute->slug]) && isset($items["{$attribute->slug}_value"]))
                unset($items[$attribute->slug], $items["{$attribute->slug}_value"]);
            }); 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $items;
    }

    public function checkVisibility(object $product, object $store): bool
    {
        try
        {
            $visibility = Attribute::whereSlug("visibility")->first();
            $visibility_option = AttributeOption::whereAttributeId($visibility?->id)->whereName("Not Visible Individually")->first();

            $is_visibility = $product->value([
                "scope" => "store",
                "scope_id" => $store->id,
                "attribute_id" => $visibility?->id
            ]);
            
           $bool = ($is_visibility?->id != $visibility_option?->id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $bool;
    }
}
