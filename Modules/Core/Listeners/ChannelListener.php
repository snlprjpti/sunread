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
        Cache::forget("sf_channel_{$channel->code}");
        CoreCache::createChannelCache($channel);
    }

    public function delete($channel)
    {
        Cache::forget("sf_channel_{$channel->code}");
    }
}
