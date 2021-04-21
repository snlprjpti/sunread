<?php

namespace Modules\Category\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

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

            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords,

            "status" => $this->status,
            "_lft" => $this->_lft,
            "_rgt" => $this->_rgt,
            "parent" => $this->parent ?? null,

            "created_at" => Carbon::parse($this->created_at)->format('M j\\,Y H:i A'),
            "translations" => $this->translations
        ];
    }
}
