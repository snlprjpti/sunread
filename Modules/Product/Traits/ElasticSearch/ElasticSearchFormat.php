<?php

namespace Modules\Product\Traits\ElasticSearch;

trait ElasticSearchFormat
{
    public function documentDataStructure(object $store): array
    {

        $array = $this->toArray();     
        $array['categories'] = $this->getCategoryData($store);
        $array['product_attributes'] = $this->getProductAttributes($store);
        // $array['product_images'] = $this->images->toArray();
        return $array;
    }

    public function getProductAttributes($store): array
    {
        $data = [];
        $product_attributes = $this->product_attributes()->with("attribute")->get();
        foreach($product_attributes as $product_attribute)
        {
            $match = [
                "scope" => "store",
                "scope_id" => $store->id,
                "attribute_id" => $product_attribute->attribute->id
            ];
            $data[$product_attribute->attribute->slug] = $this->value($match);
        }

        $inventory = $this->catalog_inventories()->select("quantity", "is_in_stock", "manage_stock", "use_config_manage_stock")->first()->toArray();
        if($inventory) $data = array_merge($data, $inventory);

        return $data;
    }

    public function getCategoryData(object $store): array
    {
        return $this->categories->map(function ($category) use ($store) {

            $defaul_data = [
                "category_id" => $category->id,
                "scope" => "store",
                "scope_id" => $store->id 
            ];

            return [
                "id" => $category->id,
                "parent_id" => $category->parent_id,
                "slug" => $category->value($defaul_data, "slug"),
                "name" => $category->value($defaul_data, "name"),
                "position" => $category->position
            ];
        })->toArray();
    }
}
