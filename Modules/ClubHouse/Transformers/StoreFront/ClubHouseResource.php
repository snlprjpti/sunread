<?php

namespace Modules\ClubHouse\Transformers\StoreFront;

use Modules\Core\Facades\CoreCache;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubHouseResource extends JsonResource
{
    public function toArray($request): array
    {

        $store = CoreCache::getStoreWithCode($request->header("hc-store"));
        $data = [
            "scope" => "website",
            "scope_id" => 1
        ];

        return [
            "id" => $this->id,
            "website_id" => $this->website_id,
            "position" => $this->position,
            "type" => $this->type,
            // "value" => $this->values,
            "title" => $this->value($data, "title"),
            "slug" => $this->value($data, "slug"),
            "status" => $this->value($data, "status"),
            "header_content" => $this->value($data, "header_content"),
            "thumbnail" => $this->value($data, "thumbnail"),
            "opening_hours" => $this->value($data, "opening_hours"),
            "address" => $this->value($data, "address"),
            "contact" => $this->value($data, "contact"),
            "latitude" => $this->value($data, "latitude"),
            "longitude" => $this->value($data, "longitude"),
            "background_type" => $this->value($data, "background_type"),
            "background_image" => $this->value($data, "background_image"),
            "meta_title" => $this->value($data, "meta_title"),
            "meta_keywords" => $this->value($data, "meta_keywords"),
            "meta_description" => $this->value($data, "meta_description"),
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
