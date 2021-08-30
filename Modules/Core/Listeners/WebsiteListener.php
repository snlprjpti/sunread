<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;

class WebsiteListener
{
    public function create($website)
    {
        CoreCache::createWebsiteCache($website);
    }

    public function beforeUpdate($website)
    {
        CoreCache::updateBeforeWebsiteCache($website);
    }

    public function update($website)
    {
        CoreCache::updateWebsiteCache($website);
    }

    public function delete($website)
    {
        CoreCache::deleteWebsiteCache($website);
    }
}
