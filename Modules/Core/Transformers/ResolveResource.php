<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Facades\SiteConfig;

class ResolveResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "code" => $this->code,
            "name" => $this->name,
            "channels" => ResolveChannelResource::collection($this->whenLoaded("channels")),
            "config" => SiteConfig::fetch("default_country", "website", $this->id),
        ];
    }
}
