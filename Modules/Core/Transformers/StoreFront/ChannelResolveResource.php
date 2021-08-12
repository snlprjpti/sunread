<?php

namespace Modules\Core\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResolveResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "code" => $this->code,
        ];
    }
}
