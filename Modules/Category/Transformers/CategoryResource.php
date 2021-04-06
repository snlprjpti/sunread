<?php

namespace Modules\Category\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class CategoryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "position" => $this->position,
            "slug" => $this->slug,
            "image" => $this->image_url,
            "status" => $this->status,
            "_lft" => $this->_lft,
            "_rgt" => $this->_rgt,
            "parent" => $this->parent ?? null,
            "created_at" => Carbon::parse($this->created_at)->format('M j\\,Y H:i A'),
            "translations" => $this->translations
        ];
    }
}
