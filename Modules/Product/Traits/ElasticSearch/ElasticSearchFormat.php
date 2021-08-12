<?php

namespace Modules\Product\Traits\ElasticSearch;

use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Tax\Entities\ProductTaxGroup;

trait ElasticSearchFormat
{
    protected $non_required_attributes = [ "cost" ],
    $options_fields = [ "select", "multiselect", "checkbox" ];

    public function documentDataStructure(object $store): array
    {
        $array = $this->getProductAttributes($store);
        $array = array_merge($array, $this->getInventoryData()); 

        $array['categories'] = $this->getCategoryData($store);
        
        $array = array_merge($array, $this->getImages());

        return $array;
    }

    public function getProductAttributes(object $store): array
    {
        $data = [];

        $data = collect($this)->filter(function ($product, $key) {
            if(in_array($key, [ "id", "sku", "status", "website_id", "parent_id", "type" ])) return $product;
        })->toArray();
        
        $attributeIds = array_unique($this->product_attributes()->pluck("attribute_id")->toArray());
        
        foreach($attributeIds as $attributeId)
        {
            $attribute = Attribute::find($attributeId);
            if(in_array($attribute->slug, $this->non_required_attributes)) continue;

            $match = [
                "scope" => "store",
                "scope_id" => $store->id,
                "attribute_id" => $attributeId
            ];

            $data[$attribute->slug] = $this->value($match);
            if(in_array($attribute->type, $this->options_fields))
            {
                $attribute_option_class = $attribute->getConfigOption() ? new ProductTaxGroup() : new AttributeOption();
                $attribute_option = $attribute_option_class->find($data[$attribute->slug]);
                if($attribute_option) $data["{$attribute->slug}_value"] = $attribute_option->name;
            }
        }

        return $data;
    }

    public function getInventoryData(): ?array
    {
        return $this->catalog_inventories()->select("quantity", "is_in_stock")->first()->toArray();
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
