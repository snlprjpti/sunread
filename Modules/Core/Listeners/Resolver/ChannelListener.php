<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Support\Facades\Cache;

class ChannelListener
{
    public function createCache($channel)
    {
        Cache::put("channel_{$channel->code}", $channel);
    }

    public function deleteCache($channel)
    {
        Cache::forget("channel_{$channel->code}");
    }
}
