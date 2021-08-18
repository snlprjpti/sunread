<?php

namespace Modules\Core\Transformers\StoreFront\Resolver;

use Illuminate\Http\Resources\Json\JsonResource;

class ResolveResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "website" => new WebsiteResource($this),
            "channel" => $this->when($this->channel, new ChannelResource($this->channel)),
            "store" => $this->when($this->store, new StoreResource($this->store)),
            "pages" => $this->when($this->pages,  PageResource::collection($this->pages)),
        ];
    }
}
