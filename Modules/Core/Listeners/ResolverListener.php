<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;

class ResolverListener
{
    public function channelResolver($channel)
    {
        Cache::put("channel_{$channel->code}", $channel);
    }

    public function removeChannelResolver($channel)
    {
        Cache::forget("channel_{$channel->code}");
    }

}
