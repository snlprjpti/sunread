<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "currency" => $this->currency,
            "name" => $this->name,
            "slug" => $this->slug,
            "locale" => $this->locale,
            "image" => $this->image_url,
            "position" => $this->position,
            "channels" => ChannelResource::collection($this->whenLoaded("channels")),
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
