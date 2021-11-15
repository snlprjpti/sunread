<?php

namespace Modules\NavigationMenu\Transformers\StoreFront;

use Exception;
use Modules\Page\Entities\Page;
use Modules\Core\Facades\CoreCache;
use Modules\Category\Entities\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            "scope" => $request->scope ?? "website",
            "scope_id" => $request->scope_id ?? $this->website_id,
            "navigation_menu_item_id" => $this->id
        ];
        return  [
            "id" => $this->id,
            "title" => $this->value($data, "title"),
            "type" => $this->value($data, "type"),
            "additional_data" => $this->value($data, "additional_data"),
            "order" => $this->value($data, "order"),
            "status" => $this->value($data, "status"),
            "link" => $this->link,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }


}
