<?php

namespace Modules\Category\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Store;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {  
        $data = [
            "scope" => "store",
            "scope_id" => $request->sf_store->id
        ];

        return [
            "id" => $this->id,
            "slug" => $this->value($data, "slug"),
            "name" => $this->value($data, "name")
        ];
    }
}
