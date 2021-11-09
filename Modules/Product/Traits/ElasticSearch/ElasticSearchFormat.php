<?php

namespace Modules\Product\Traits\ElasticSearch;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Product\Entities\ImageType;
use Modules\Tax\Entities\ProductTaxGroup;

trait ElasticSearchFormat
{
    protected $non_required_attributes = [ "cost" ],
    $options_fields = [ "select", "multiselect", "checkbox" ];

    public function documentDataStructure(object $store): array
    {
        try
        {
            $array = $this->getProductAttributes($store);

            $inventory = $this->getInventoryData();
            if ($inventory) {
                $array = array_merge($array, $inventory); 
                $array["stock_status_value"] = ($array["is_in_stock"] == 1) ? "In stock" : "Out of stock";
            }
    
            $array['categories'] = $this->getCategoryData($store);
            $images = $this->getImages();
            if($this->type == "simple" && !$this->parent_id) $array["list_status"] = 1;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return array_merge($array, $images);
    }

    public function getProductAttributes(object $store): array
    {
        try
        {   
            $data = $this->select("id", "sku", "status", "website_id", "parent_id", "type")->where("id", $this->id)->first()->toArray();
            
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

                if(in_array($attribute->type, $this->options_fields))
                {
                    $values = $this->value($match);
                    if ($values instanceof Collection) {
                        $data[$attribute->slug] = $values->pluck("id")->toArray();
                        foreach($values as $key => $val)
                        {
                            $data["{$attribute->slug}_{$key}_value"] = $val?->name;
                        }
                    }
                    else {
                        $data[$attribute->slug] = $values?->id;
                        $data["{$attribute->slug}_value"] = $values?->name;
                    }
                }
                else  $data[$attribute->slug] = $this->value($match);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function getAttributeOption(object $attribute, mixed $value): ?string
    {
        try
        {
            $attribute_option_class = $attribute->getConfigOption() ? new ProductTaxGroup() : new AttributeOption();
            $attribute_option = $attribute_option_class->find($value);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $attribute_option?->name;
    }

    public function getInventoryData(): ?array
    {
        try
        {
            $inventory = $this->catalog_inventories()->select("quantity", "is_in_stock")->first()?->toArray();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $inventory;
    }

    public function getCategoryData(object $store): array
    {
        try
        {
            $categories = $this->categories->map(function ($category) use ($store) {

                $defaul_data = [
                    "category_id" => $category->id,
                    "scope" => "store",
                    "scope_id" => $store->id 
                ];
    
                return [
                    "id" => $category->id,
                    "slug" => $category->value($defaul_data, "slug"),
                    "name" => $category->value($defaul_data, "name")
                ];
            })->toArray();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $categories;
    }

    public function getImages(): array
    {
        try
        {
            $image_types = ImageType::where("slug", "!=", "gallery")->get();
            foreach($image_types as $image_type) $images[$image_type->slug] = $this->getFullPath("base_image");

            $images['gallery'] = $this->images()->wherehas("types", function($query) {
                $query->whereSlug("gallery");
            })->get()->map(function ($gallery) {
                return [
                    "url" => Storage::url($gallery->path),
                    "background_color" => $gallery->background_color
                ];
            })->toArray();
            
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $images;
    }

    public function getFullPath($image_name): ?array
    {
        try
        {
            $image = $this->images()->wherehas("types", function($query) use($image_name) {
                $query->whereSlug($image_name);
            })->first();
            $path = $image ? Storage::url($image->path) : $image;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
         
        return [
            "url" => $path,
            "background_color" => $image?->background_color
        ];
    }
}
