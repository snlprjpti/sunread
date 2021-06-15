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
            "scope" => $this->scope,
            "validation" => $this->validation,
            "position" => $this->position,
            "is_required" => $this->is_required,
            "is_searchable" => $this->is_searchable,
            "use_in_layered_navigation" => $this->use_in_layered_navigation,
            "is_visible_on_storefront" => $this->is_visible_on_storefront,
            "is_user_defined" => $this->is_user_defined,
            "comparable_on_storefront" => $this->comparable_on_storefront,
            "options" => AttributeOptionResource::collection($this->whenLoaded("attribute_options")),
            "translations" => $this->whenLoaded("translations"),
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
