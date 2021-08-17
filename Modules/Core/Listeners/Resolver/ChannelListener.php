<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Support\Facades\Cache;

class ChannelListener
{
    public function updateChannel($channel)
    {
        Cache::put("channel_{$channel->code}", $channel);
    }

    public function deleteChannel($channel)
    {
        Cache::forget("channel_{$channel->code}");
    }
}
