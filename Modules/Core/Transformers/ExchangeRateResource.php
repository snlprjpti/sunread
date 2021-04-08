<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "source_currency" => $this->source_currency,
            "target_currency" => $this->target_currency,
            "rate" => $this->rate,
            "updated_at" => $this->created_at,
            "created_at" => $this->updated_at,
            'source' => $this->source,
            'target' => $this->target
        ];
    }
}
