<?php

namespace Modules\Tax\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateTaxRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "tax_rate" => $this->whenLoaded("tax_rate"),
            "tax_rule" => $this->whenLoaded("tax_rule")
        ];
    }
}
