<?php

namespace Modules\NavigationMenu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuItemResource extends JsonResource
{
    public function toArray($request): array
    {

        $data = [
            "scope" => $request->scope ?? "website",
            "scope_id" => $request->scope_id ?? $this->navigationMenu->website_id,
        ];

        return [
            "id" => $this->id,
            "navigation_menu_id" => $this->navigation_menu_id,
            "navigation_menu_title" => $this->navigationMenu->title,
            "title" => $this->value($data, "title"),
            "type_id" => $this->value($data, "type_id"),
            "custom_link" => $this->value($data, "custom_link"),
            "type" => $this->value($data, "type"),
            "additional_data" => $this->value($data, "additional_data"),
            "order" => $this->value($data, "order"),
            "status" => $this->value($data, "status"),
            "values" => $this->values,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
