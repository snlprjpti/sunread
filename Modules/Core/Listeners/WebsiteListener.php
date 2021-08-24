<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Facades\CoreCache;

class WebsiteListener
{
    public function create($website)
    {
        CoreCache::createWebsiteCache($website);
    }

    public function update($website)
    {
        Cache::forget("sf_website_{$website->hostname}");
        CoreCache::createWebsiteCache($website);
    }

    public function delete($website)
    {
        Cache::forget("sf_website_{$website->hostname}");
    }
}
