<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Category\Transformers\CategoryResource;

class ChannelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "code" => $this->code,
            "hostname" => $this->hostname,
            "name" => $this->name,
            "description" => $this->description,
            "location" => $this->location,
            "timezone" => $this->timezone,
            "logo" => $this->logo_url,
            "favicon" => $this->favicon_url,
            "theme" => $this->theme,
            "default_currency" => $this->default_currency,
            "default_store" => new StoreResource($this->whenLoaded("default_store")),
            "stores" => StoreResource::collection($this->whenLoaded("stores")),
            "stores_count" => $this->when($this->relationLoaded("stores"), $this->stores->count()),
            "website" => new WebsiteResource($this->whenLoaded("website")),
            "default_category" => new CategoryResource($this->whenLoaded("default_category")),
            "status" => (bool) $this->status,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
