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
        $name_data = array_merge($data, ["attribute" => "name"]);
        $nameValue = $this->has($name_data) ? $this->getValues($name_data) : $this->getDefaultValues($name_data);
        $slug_data = array_merge($data, ["attribute" => "slug"]);
        $slugValue = $this->has($slug_data) ? $this->getValues($slug_data) : $this->getDefaultValues($slug_data);

        return [
            "id" => $this->id,
            "slug" => $slugValue ? $slugValue->value : null,
            "name" => $nameValue ? $nameValue->value : null,
            "children" => CategoryResource::collection($this->children->sortBy('_lft'))
        ];
    }
}
