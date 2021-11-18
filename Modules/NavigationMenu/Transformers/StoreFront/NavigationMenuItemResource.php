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
        $store = CoreCache::getStoreWithCode($request->header("hc-store"));
        $data = [
            "scope" => "store",
            "scope_id" => $store->id,
            "navigation_menu_item_id" => $this->id
        ];
        return  [
            "id" => $this->id,
            "title" => $this->value($data, "title"),
            "type" => $this->value($data, "type"),
            "order" => $this->value($data, "order"),
            "background_type" => $this->value($data, "background_type"),
            "background_image" => $this->value($data, "background_image"),
            "background_video_type" => $this->value($data, "background_video_type"),
            "background_video" => $this->value($data, "background_video"),
            "background_overlay_color" => $this->value($data, "background_overlay_color"),
            "status" => $this->value($data, "status"),
            "link" => $this->link,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }


}
