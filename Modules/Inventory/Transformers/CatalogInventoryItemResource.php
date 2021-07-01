<?php

namespace Modules\Inventory\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\ProductResource;

class CatalogInventoryItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "product" => new ProductResource($this->whenLoaded("product")),
            "event" => $this->event,
            "order_id" => $this->order_id,
            "adjusted_by" => $this->adjusted_by,
            "adjustment_type" => $this->adjustment_type,
            "quantity" => $this->quantity,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
