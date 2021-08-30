<?php

namespace Modules\Product\Traits\ElasticSearch;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Website;

trait ConfigurableProductHandler
{
    use HasIndexing;

    public function createProduct(object $parent): void
    {
        try
        {
            $stores = Website::find($parent->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });
            $variants = $parent->variants()->with("categories", "product_attributes", "catalog_inventories")->get();

            $state = [];
            $items = [];
            foreach($variants as $variant)
            {
                $variant_attributes = $variant->attribute_configurable_products()->pluck("attribute_id", "attribute_option_id")->toArray();
                $color_attribute = Attribute::whereSlug("color")->first();
                $group_by_attribute = in_array($color_attribute->id, $variant_attributes) ? $color_attribute->id : $variant_attributes[array_key_first($variant_attributes)];
                $group_by_option = array_keys($variant_attributes, $group_by_attribute)[0];

                foreach($stores as $store)
                {
                    if(!isset($state[$group_by_option][$store->id])) {
                        $state[$group_by_option][$store->id] = $variant;

                        $items[$variant->id][$store->id] = $variant->documentDataStructure($store); 

                        $items = $this->getGroupAttributes($items, $variant_attributes, $variant, $store, $group_by_attribute, "main");    
                    }
                    else {
                        $prev_product = $state[$group_by_option][$store->id];
                        $items = $this->getGroupAttributes($items, $variant_attributes, $prev_product, $store, $group_by_attribute);  
                    }
                }
            }
            
            $this->configurableIndexing($items);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function getGroupAttributes(array $items, array $variant_attributes, object $variant, object $store, int $group_by_attribute, ?string $main = null): array
    {
        foreach($variant_attributes as $key => $variant_attribute)
        {
            if($variant_attribute == $group_by_attribute) continue;

            $attribute_data = Attribute::find($variant_attribute);
            if($attribute_data) {

                $attribute_option = AttributeOption::find($key);
                $items[$variant->id][$store->id]["configurable_{$attribute_data->slug}"][] = $key;
                $items[$variant->id][$store->id]["configurable_{$attribute_data->slug}_value"][] = $attribute_option?->name;

                $items[$variant->id][$store->id]["configurable"][$attribute_data->slug][] = [
                    "label" => $attribute_option?->name,
                    "value" => $key
                ];

                if($main) unset($items[$variant->id][$store->id][$attribute_data->slug], $items[$variant->id][$store->id]["{$attribute_data->slug}_value"]);   
            }
        } 
        
        return $items;
    }

 
}
