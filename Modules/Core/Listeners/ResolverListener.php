<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;

class ResolverListener
{
    public function channelResolver($channel)
    {
        Cache::rememberForever('channel_'.$channel->code, function() use($channel){
            return $channel->first();
        });
    }

}
