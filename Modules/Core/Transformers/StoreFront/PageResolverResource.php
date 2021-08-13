<?php

namespace Modules\Core\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResolverResource extends JsonResource
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
