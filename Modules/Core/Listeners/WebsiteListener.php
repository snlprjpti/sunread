<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Website;
use Modules\Core\Jobs\CoreCacheJob;

class WebsiteListener
{
    public function create($website)
    {
        CoreCacheJob::dispatch( "createWebsiteCache", $website );
    }

    public function beforeUpdate($website_id)
    {
        $website = Website::findOrFail($website_id);
//        dd(collect($website));
        CoreCacheJob::dispatch( "updateBeforeWebsiteCache", collect($website) );
    }

    public function update($website)
    {
        CoreCacheJob::dispatch( "updateWebsiteCache", $website );
    }

    public function beforeDelete($website_id)
    {
        $website = Website::findOrFail($website_id);
        CoreCacheJob::dispatch( "deleteBeforeWebsiteCache", $website );
    }

    public function delete($website)
    {
        CoreCacheJob::dispatch( "deleteWebsiteCache", $website );
    }
}
