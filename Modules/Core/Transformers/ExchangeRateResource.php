<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "source_currency" => $this->source_currency,
            "target_currency" => $this->target_currency,
            "rate" => $this->rate,
            "updated_at" => $this->created_at,
            "created_at" => $this->updated_at,
            'source' => $this->whenLoaded("source"),
            'target' => $this->whenLoaded("target")
        ];
    }
}
