<?php

namespace Modules\ClubHouse\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubHouseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "website_id" => $this->website_id,
            "position" => $this->position,
            "type" => $this->type,
            "values" => $this->values,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
