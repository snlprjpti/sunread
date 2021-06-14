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
            // "attribute_group" => new AttributeGroupResource($this->whenLoaded("attribute_group")),
            "validation" => $this->validation,
            "position" => $this->position,
            "is_required" => $this->is_required,
            "use_in_layered_navigation" => $this->use_in_layered_navigation,
            "is_visible_on_storefront" => $this->is_visible_on_storefront,
            "is_user_defined" => $this->is_user_defined,
            "options" => AttributeOptionResource::collection($this->whenLoaded("attribute_options")),
            "translations" => $this->translations,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
