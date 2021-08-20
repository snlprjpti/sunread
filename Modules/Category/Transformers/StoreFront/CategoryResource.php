<?php

namespace Modules\Category\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Facades\Resolver;
use Modules\Core\Traits\WebsiteResolveable;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    { 
        $website = Resolver::fetch($request); 
        $data = [
            "scope" => "store",
            "scope_id" => $website["store"]["id"]
        ];

        return [
            "id" => $this->id,
            "slug" => $this->value($data, "slug"),
            "name" => $this->value($data, "name")
        ];
    }
}
