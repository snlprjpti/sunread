<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Transformers\AttributeResource;

class ProductAttributeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "attribute" => new AttributeResource($this->attribute),
            "value" => $this->value_data
        ];
    }
}
