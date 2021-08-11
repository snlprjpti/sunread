<?php

namespace Modules\Product\Traits\ElasticSearch;

trait ElasticSearchFormat
{
    public function documentDataStructure(object $store): array
    {

        $array = $this->toArray();     
        $array['categories'] = $this->getCategoryData($store);
        dd($array);
        $array['product_attributes'] = $this->getProductAttributes();
       // $array['product_images'] = $this->images->toArray();
        $array['catalog_inventories'] = $this->catalog_inventories->toArray();
        dd($array);
        return $array;
    }

    public function getProductAttributes(): array
    {
        $data = [];
        $product_attributes = $this->product_attributes()->with("attribute")->get();
        foreach($product_attributes as $product_attribute)
        {
            $data[$product_attribute->attribute->slug] = $product_attribute->value?->value;
        }

        return $data;
    }

    public function getCategoryData(object $store)
    {
        return $this->categories->map(function ($category) use ($store) {

            $category->createModel();

            $defaul_data = [
                "category_id" => $category->id,
                "scope" => "store",
                "scope_id" => $store->id 
            ];

            $slug_data = array_merge( $defaul_data, ["attribute" => "slug"] );
            $name_data = array_merge( $defaul_data, ["attribute" => "name"] );

            $slug = $category->has($slug_data) ? $category->getValues($slug_data) : $category->getDefaultValues($slug_data);
            $name = $category->has($name_data) ? $category->getValues($name_data) : $category->getDefaultValues($name_data);

            return [
                "id" => $category->id,
                "parent_id" => $category->parent_id,
                "slug" => $slug?->value,
                "name" => $name?->value,
                "position" => $category->position
            ];
        })->toArray();
    }
}
