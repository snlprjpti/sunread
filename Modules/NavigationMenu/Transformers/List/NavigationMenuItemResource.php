<?php

namespace Modules\NavigationMenu\Transformers\List;

use Modules\Core\Facades\CoreCache;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return  [
            "id" => $this->id,
            "scope" => $this->scope,
            "scope_id" => $this->scope_id,
            "attribute" => $this->attribute,
            "values" => $this->values,
            "children" => NavigationMenuItemResource::collection($this->children),
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }

}
