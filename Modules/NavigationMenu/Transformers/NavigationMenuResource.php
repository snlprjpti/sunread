<?php

namespace Modules\NavigationMenu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "status" => $this->status,
            "location" => $this->location,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
