<?php

namespace Modules\Core\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;

class ResolveResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "website" => new WebsiteResolveResource($this),
            "channel" => $this->when($this->channel, new ChannelResolveResource($this->channel)),
            "store" => $this->when($this->store, new StoreResolveResource($this->store)),
            "pages" => $this->when($this->pages,  PageResolverResource::collection($this->pages)),
        ];
    }
}
