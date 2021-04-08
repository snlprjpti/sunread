<?php

namespace Modules\Attribute\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            "slug" => $this->slug,
            "name" => $this->name,
            "type" => $this->type,
            "attribute_group" => $this->attribute_group ?? null,
            "validation" => $this->validation,
            "position" => $this->position,
            "is_required" => $this->is_required,
            "is_unique" => $this->is_unique,
            "is_filterable" => $this->is_filterable,
            "is_user_defined" => $this->is_user_defined,
            "use_in_flat" => $this->use_in_flat,
            "is_visible_on_front" => $this->is_visible_on_front,
            "created_at" => Carbon::parse($this->created_at)->format('M j\\,Y H:i A'),
            "translations" => $this->translations,
            "options" => $this->attribute_options ?? null
        ];
    }
}
