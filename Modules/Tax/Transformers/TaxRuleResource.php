<?php

namespace Modules\Tax\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "customer_group" => new CustomerTaxGroupResource($this->whenLoaded("customer_group")),
            "product_taxable" => new ProductTaxGroupResource($this->whenLoaded("product_taxable")),
            "name" => $this->name,
            "position" => (int) $this->position,
            "priority" => (int) $this->priority,
            "subtotal" => (float) $this->subtotal,
            "status" => (bool) $this->status,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
