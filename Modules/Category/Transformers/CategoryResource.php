<?php

namespace Modules\Category\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\ChannelResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $scopeValue = $this->values()->whereScope($request->scope ?? "website")->whereScopeId($request->scope_id ?? $request->website_id)->first();
        
        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "name" => $scopeValue->name,
            "status" => $scopeValue->status,
            "include_in_menu" => $scopeValue->include_in_menu,
            "children" => CategoryResource::collection($this->children)
        ];
    }
}
