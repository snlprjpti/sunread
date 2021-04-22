<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
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
            "default_store" => new StoreResource($this->default_store),
            "stores" => StoreResource::collection($this->stores),
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }

}
