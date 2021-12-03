<?php

namespace Modules\NavigationMenu\Transformers\StoreFront;

use Modules\Core\Facades\CoreCache;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $store = CoreCache::getStoreWithCode($request->header("hc-store"));
        $channel = CoreCache::getChannelWithCode($request->header("hc-channel"));
        $data = [
            "scope" => "store",
            "scope_id" => $store->id,
            "navigation_menu_item_id" => $this->id
        ];

        return  [
            "id" => $this->id,
            "title" => $this->value($data, "title"),
            "type" => $this->value($data, "type"),
            "background_type" => $this->value($data, "background_type"),
            "background_image" => $this->value($data, "background_image"),
            "background_video_type" => $this->value($data, "background_video_type"),
            "background_video" => $this->value($data, "background_video"),
            "background_overlay_color" => $this->value($data, "background_overlay_color"),
            "status" => (int) $this->value($data, "status"),
            "link" => $this->getFinalItemLink($store, $channel),
            "parent_id" => $this->parent_id,
            "position" => $this->position,
            "children" => NavigationMenuItemResource::collection($this->children),
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }

}
