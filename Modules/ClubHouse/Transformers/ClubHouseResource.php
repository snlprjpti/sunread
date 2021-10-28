<?php

namespace Modules\ClubHouse\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\ChannelResource;

class ClubHouseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "website_id" => $this->website_id,
            "position" => $this->position,
            "values" => $this->values,

            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
