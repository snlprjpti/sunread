<?php

namespace Modules\Core\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Facades\SiteConfig;

class ChannelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "code" => $this->code,
            "icon" => SiteConfig::fetch("channel_icon", "channel", $this->id)
        ];
    }
}
