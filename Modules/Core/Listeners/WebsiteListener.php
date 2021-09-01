<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Jobs\CoreCacheJob;

class WebsiteListener
{
    public function create($website)
    {
        CoreCacheJob::dispatch("createWebsiteCache", $website);
    }
//
//    public function beforeUpdate($website_id)
//    {
//        CoreCache::updateBeforeWebsiteCache($website_id);
//    }
//
//    public function update($website)
//    {
//        CoreCache::updateWebsiteCache($website);
//    }
//
//    public function beforeDelete($website)
//    {
//        CoreCache::deleteBeforeWebsiteCache($website);
//    }
//
//    public function delete($website)
//    {
//        CoreCache::deleteWebsiteCache($website);
//    }
}
