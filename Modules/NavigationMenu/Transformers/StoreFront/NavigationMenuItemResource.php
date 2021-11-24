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
            "order" => (int) $this->value($data, "order"),
            "background_type" => $this->value($data, "background_type"),
            "background_image" => $this->value($data, "background_image"),
            "background_video_type" => $this->value($data, "background_video_type"),
            "background_video" => $this->value($data, "background_video"),
            "background_overlay_color" => $this->value($data, "background_overlay_color"),
            "status" => (int) $this->value($data, "status"),
            "link" => $this->getFinalItemLink($this, $store, $channel),
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }


    public function getFinalItemLink(object $navigation_menu_item, $store, $channel)
    {

        $store_data = [
            "scope" => "store",
            "scope_id" => $store->id,
        ];

        $type = $navigation_menu_item->value($store_data, "type");
        switch ($type) {
            case 'category':
                $type_id = $navigation_menu_item->value($store_data, "category_id");
                $category = Category::find($type_id);
                $slug = $category ? $category->value($store_data, "slug") : "";
                $link = $this->getDynamicLink($slug, $store, $channel, "category/");
                return $link;
                break;

            case 'page':
                $type_id = $navigation_menu_item->value($store_data, "page_id");
                $page = Page::find($type_id);
                $link = $this->getDynamicLink($page ? $page->slug : null, $store, $channel, "page/");
                return $link;
                break;

            case 'custom':
                $custom_link = $navigation_menu_item->value($store_data, "custom_link");
                return $custom_link;
                break;

            case 'dynamic':
                $dynamic_link = $navigation_menu_item->value($store_data, "dynamic_link");
                $link = $this->getDynamicLink($dynamic_link, $store, $channel);
                return $link;
                break;

            default:
                return null;
                break;
        }
    }

    public function getDynamicLink(?string $slug, object $store, $channel, ?string $prepend = null): mixed
    {
        try
        {
            $default_url = "{$channel->code}/{$store->code}/{$prepend}{$slug}";
            $final_url = $default_url;
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $final_url;
    }


}
