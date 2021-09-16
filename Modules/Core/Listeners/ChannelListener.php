<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Channel;
use Modules\Core\Jobs\CoreCacheJob;

class ChannelListener
{
    public function create(object $channel): void
    {
        CoreCacheJob::dispatch( "createChannelCache", $channel )->onQueue("high");
    }

    public function beforeUpdate(int $channel_id): void
    {
        $channel = Channel::findOrFail($channel_id);
        CoreCacheJob::dispatch( "updateBeforeChannelCache", collect($channel) )->onQueue("high");
    }

    public function update(object $channel): void
    {
        CoreCacheJob::dispatch( "updateChannelCache", $channel )->onQueue("high");
    }

    public function beforeDelete(int $channel_id): void
    {
        $channel = Channel::findOrFail($channel_id);
        CoreCacheJob::dispatch( "deleteBeforeChannelCache", collect($channel) )->onQueue("high");
    }

    public function delete(object $channel): void
    {
        CoreCacheJob::dispatch( "deleteChannelCache", collect($channel) )->onQueue("high");
    }
}
