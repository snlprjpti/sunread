<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Channel;
use Modules\Core\Facades\CoreCache;

class ChannelListener
{
    public function create($channel)
    {
        CoreCache::createChannelCache($channel);
    }

    public function beforeUpdate($channel)
    {
        CoreCache::updateBeforeChannelCache($channel);
    }

    public function update($channel)
    {
        CoreCache::updateChannelCache($channel);
    }

    public function delete($channel)
    {
        CoreCache::deleteChannelCache($channel);
    }
}
