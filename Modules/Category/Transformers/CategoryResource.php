<?php

namespace Modules\Category\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\ChannelResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "position" => $this->position,
            "image" => $this->image_url,
            "description" => $this->description,
            "default_url" => $this->url,

            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords,

            "status" => (bool) $this->status,
            "_lft" => $this->_lft,
            "_rgt" => $this->_rgt,
            "parent" => $this->whenLoaded("parent"),

            "created_at" => $this->created_at->format('M d, Y H:i A'),
            "translations" => $this->whenLoaded("translations"),
            "channels" => ChannelResource::collection($this->whenLoaded("channels")),
        ];
    }
}
