<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Transformers\AttributeGroupResource;
use Modules\Brand\Transformers\BrandResource;
use Modules\Category\Transformers\CategoryResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $variants = ($this->variants->count() > 0) ? ["variants" => ProductResource::collection($this->variants)] : [];
        return array_merge([
            "id" => $this->id,
            "parent" => new ProductResource($this->whenLoaded("parent")),
            "brand" => new BrandResource($this->whenLoaded("brand")),
            "attribute_group" => new AttributeGroupResource($this->whenLoaded("attribute_group")),
            "sku" => $this->sku,
            "type" => $this->type,
            "status" => $this->status,
            "categories" => CategoryResource::collection($this->whenLoaded("categories")),
            "attribute_values" => ProductAttributeResource::collection($this->whenLoaded("product_attributes")),
            "images" => ProductImageResource::collection($this->whenLoaded("images")),
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ], $variants);
    }
}
