<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ResolveChannelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "code" => $this->code,
            "stores" => ResolveStore♦Resource::collection($this->whenLoaded("stores")),
        ];
    }
}
