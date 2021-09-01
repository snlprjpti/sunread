<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Channel;
use Modules\Core\Jobs\CoreCacheJob;

class ChannelListener
{
    public function create($channel)
    {
        CoreCacheJob::dispatch( "createChannelCache", $channel );
    }

    public function beforeUpdate($channel_id)
    {
        $channel = Channel::findOrFail($channel_id);
        CoreCacheJob::dispatch( "updateBeforeChannelCache", collect($channel) );
    }

    public function update($channel)
    {
        CoreCacheJob::dispatch( "updateChannelCache", $channel );
    }

    public function beforeDelete($channel_id)
    {
        $channel = Channel::findOrFail($channel_id);
        CoreCacheJob::dispatch( "deleteBeforeChannelCache", collect($channel) );
    }

    public function delete($channel)
    {
        CoreCacheJob::dispatch( "deleteChannelCache", collect($channel) );
    }
}
