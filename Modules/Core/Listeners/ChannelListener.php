<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;

class ChannelListener
{
    public function create($channel)
    {
        CoreCache::createChannelCache($channel);
    }

    public function update($channel)
    {
        CoreCache::createChannelCache($channel);
    }

    public function delete($channel)
    {
        $website = $channel->website;
        Cache::forget("sf_website_{$website->hostname}_channel_{$channel->code}");
    }
}
