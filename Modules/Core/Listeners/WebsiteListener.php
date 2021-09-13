<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Website;
use Modules\Core\Jobs\CoreCacheJob;

class WebsiteListener
{
    public function create(object $website): void
    {
        CoreCacheJob::dispatch( "createWebsiteCache", $website );
    }

    public function beforeUpdate(int $website_id): void
    {
        $website = Website::findOrFail($website_id);
        CoreCacheJob::dispatch( "updateBeforeWebsiteCache", collect($website) );
    }

    public function update(object $website): void
    {
        CoreCacheJob::dispatch( "updateWebsiteCache", $website );
    }

    public function beforeDelete(int $website_id): void
    {
        $website = Website::findOrFail($website_id);
        CoreCacheJob::dispatch( "deleteBeforeWebsiteCache", collect($website) );
    }

    public function delete(object $website): void
    {
        CoreCacheJob::dispatch( "deleteWebsiteCache", collect($website) );
    }
}
