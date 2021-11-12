<?php

namespace Modules\NavigationMenu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "navigation_menu_id" => $this->navigation_menu_id,
            "navigation_menu_title" => $this->navigationMenu->title,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
