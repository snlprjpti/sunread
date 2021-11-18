<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderTaxResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
           "id" => $this->id,
           "tax_items" => OrderTaxItemsResource::collection($this->whenLoaded("tax_items")),
           "code" => $this->code,
           "title" => $this->title,
           "percent" => $this->percent,
           "amount" => $this->amount,
           "created_at" => $this->created_at?->format("M d, Y H:i A")
        ];
    }
}
