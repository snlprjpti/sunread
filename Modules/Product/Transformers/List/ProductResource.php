<?php

namespace Modules\Product\Transformers\List;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Transformers\CategoryResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $attributeIdforName = Attribute::whereSlug("name")->first()->id;
        $attributeIdforVisibility = Attribute::whereSlug("visibility")->first()->id;
        $product_attributes = $this->product_attributes()->whereScope($request->scope ?? "website")->whereScopeId($request->scope_id ?? $this->website_id);
        $name = $product_attributes->whereAttributeId($attributeIdforName)->first();
        $visibility = $this->product_attributes()->whereScope($request->scope ?? "website")->whereScopeId($request->scope_id ?? $this->website_id)->whereAttributeId($attributeIdforVisibility)->first();
        $images = $this->images()->where('main_image', 1)->first();
        $stock = $this->catalog_inventories()->first();

        return [
            "id" => $this->id,
            "name" => $name->value_data,
            "type" => $this->type,
            "sku" => $this->sku,           
            "stock" => $stock ? $stock->quantity : null,
            "status" => (bool) $this->status,
            "categories" => CategoryResource::collection($this->whenLoaded("categories")),
            "visibility" => $visibility->value_data,
            "images" => $images ? Storage::url($images->path) : null,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
