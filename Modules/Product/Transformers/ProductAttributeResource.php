<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "attribute" => [
                "name" => $this->attribute->name,
                "slug" => $this->attribute->slug,
                "type" => $this->attribute->type
            ],
            "value" => $this->value_data
        ];
    }
}
