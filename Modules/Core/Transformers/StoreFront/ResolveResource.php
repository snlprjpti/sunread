<?php

namespace Modules\Core\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Configuration;
use Modules\Core\Entities\Store;

class ResolveResource extends JsonResource
{
    public function toArray($request): array
    {
        if($request->hasHeader("hc-channel")){
            $channel_code = $request->header("hc-channel");
            $channel = Channel::whereCode($channel_code)->firstOrFail();
        }
        else {
            $channel = ($channel = $this->checkConditionChannel()->first()) ? Channel::whereId($channel->value)->firstOrFail() : null;
        }

        if($request->hasHeader("hc-store")){
            $store_code = $request->header("hc-store");
            $store = Store::whereCode($store_code)->firstOrFail();
        }
        else {
            $store = ($store = $this->checkConditionStore()->first()) ? Store::whereId($store->value)->firstOrFail() : null;
        }

        return [
            "website" => new WebsiteResolveResource($this),
            "channel" => new ChannelResolveResource($channel),
            "store" => new StoreResolveResource($store)
        ];
    }

    public function checkConditionChannel(): object
    {
        return Configuration::where([
            ['path', "website_default_channel"]
        ]);
    }

    public function checkConditionStore(): object
    {
        return Configuration::where([
            ['path', "website_default_store"]
        ]);
    }
}
