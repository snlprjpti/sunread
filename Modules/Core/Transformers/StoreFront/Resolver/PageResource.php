<?php

namespace Modules\Core\Transformers\StoreFront\Resolver;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            $this->title =>
                [
                    "id"=>$this->id,
                    "code"=>$this->slug
                ]
        ];
    }
}
