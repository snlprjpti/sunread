<?php

namespace Modules\Product\Traits\ElasticSearch;

use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;

trait ElasticSearchFormat
{
    public function documentDataStructure(object $store): array
    {
        $array = $this->getBasicAttribute($store); 
        $array = array_merge($array, $this->getInventoryData()); 

        $array['categories'] = $this->getCategoryData($store);
        $array['product_attributes'] = $this->getProductAttributes($store);
        
        $array = array_merge($array, $this->getImages());

        return $array;
    }

    public function getBasicAttribute(object $store): array
    {
        $slugs = [ "name", "price" ];
        $data = [];

        $data = collect($this)->filter(function ($product, $key) {
            if(in_array($key, ["id", "sku", "status", "website_id", "parent_id", "type", "attribute_set_id"])) return $product;
        })->toArray();

        foreach($slugs as $slug)
        {
            $attribute = Attribute::whereSlug($slug)->first();
            $match = [
                "scope" => "store",
                "scope_id" => $store->id,
                "attribute_id" => $attribute->id
            ];
            $data[$slug] = $this->value($match);
        }
        return $data;
    }

    public function getInventoryData(): ?array
    {
        return $this->catalog_inventories()->select("quantity", "is_in_stock", "manage_stock", "use_config_manage_stock")->first()->toArray();
    }

    public function getProductAttributes(object $store): array
    {
        $data = [];
        $attributeIds = array_unique($this->product_attributes()->pluck("attribute_id")->toArray());
        
        foreach($attributeIds as $attributeId)
        {
            $attribute = Attribute::find($attributeId);
            $attribute_type = config("attribute_types")[$attribute->type ?? "string"];
            $model_type = new $attribute_type();
            $type = $model_type::$type;

            $match = [
                "scope" => "store",
                "scope_id" => $store->id,
                "attribute_id" => $attributeId
            ];

            $data[] = [
                "attribute" => $attribute->toArray(),
                "value" => $this->value($match),
                "{$type}_value" => $this->value($match),
            ];
        }
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

    public function getImages(): array
    {
        return [
            'base_image' => $this->getFullPath("main_image"),
            'thumbnail_image' => $this->getFullPath("thumbnail"),
            'small_image' => $this->getFullPath("small_image"),
            'section_background' => $this->getFullPath("section_background"),
            'gallery' => $this->images()->whereGallery(1)->pluck('path')->map(function ($gallery) {
                return Storage::url($gallery);
            })->toArray()
        ];
    }

    Public function getFullPath($image_name): ?string
    {
        $image = $this->images()->where($image_name, 1)->first();
        return $image ? Storage::url($image->path) : $image;
    }
}
