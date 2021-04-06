<?php

namespace Modules\Attribute\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class AttributeGroupResource extends Resource
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
            "name" => $this->name,
            "slug" => $this->slug,
            "position" => $this->position,
            "is_user_defined" => $this->is_user_defined,
            "attribute_family" => $this->attribute_family
        ];
    }
}
