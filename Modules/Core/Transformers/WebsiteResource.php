<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "code" => $this->code,
            "hostname" => $this->hostname,
            "name" => $this->name,
            "position" => $this->position,
            "description" => $this->description,
            "channels" => ChannelResource::collection($this->whenLoaded("channels")),
            "channels_count" => $this->when($this->relationLoaded("channels"), $this->channels->count()),
            "status" => $this->status,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
