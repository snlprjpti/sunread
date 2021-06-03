<?php

namespace Modules\Attribute\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "name" => $this->name,
            "type" => $this->type,
            "attribute_group" => new AttributeGroupResource($this->attribute_group),
            "validation" => $this->validation,
            "position" => $this->position,
            "is_required" => $this->is_required,
            "is_unique" => $this->is_unique,
            "is_filterable" => $this->is_filterable,
            "is_visible_on_front" => $this->is_visible_on_front,
            "is_user_defined" => $this->is_user_defined,
            "options" => AttributeOptionResource::collection($this->whenLoaded("attribute_options")),
            "translations" => $this->translations,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
