<?php

namespace Modules\NavigationMenu\Transformers\StoreFront;

use Modules\Core\Facades\CoreCache;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\NavigationMenu\Transformers\StoreFront\NavigationMenuItemResource;

class NavigationMenuResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "location" => $this->location,
            "website_id" => $this->website_id,
            "items" => NavigationMenuItemResource::collection($this->navigationMenuItems),
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
