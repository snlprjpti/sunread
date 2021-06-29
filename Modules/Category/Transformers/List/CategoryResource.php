<?php

namespace Modules\Category\Transformers\List;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\ChannelResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {    
        $this->createModel();
        $data = [
            "scope" => $request->scope ?? "website",
            "scope_id" => $request->scope_id ?? $request->website_id,
            "category_id" => $this->id
        ];
        $scopeValue = $this->checkCondition($data) ? $this->getValues($data) : $this->getDefaultValues($data);

        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "name" => $scopeValue->name,
            "children" => CategoryResource::collection($this->children)
        ];
    }
}
