<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Transformers\AttributeGroupResource;
use Modules\Brand\Transformers\BrandResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "parent" => new ProductResource($this->whenLoaded("parent")),
            "brand" => new BrandResource($this->whenLoaded("brand")),
            "attribute_group" => new AttributeGroupResource($this->whenLoaded("attribute_group")),
            "sku" => $this->sku,
            "type" => $this->type,
            "status" => $this->status,
            "attribute_values" => ProductAttributeResource::collection($this->whenLoaded("product_attributes")),
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
