<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;

class WebsiteListener
{
    public function create($website)
    {
        CoreCache::createWebsiteCache($website);
    }

    public function update($website)
    {
        CoreCache::createWebsiteCache($website);
    }

    public function delete($website)
    {
        Cache::forget("sf_website_{$website->hostname}");
    }
}
