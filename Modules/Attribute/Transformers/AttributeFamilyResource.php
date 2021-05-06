<?php

namespace Modules\Attribute\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeFamilyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "name" => $this->name,
            "status" => $this->status,
            "is_user_defined" => $this->is_user_defined
        ];
    }
}