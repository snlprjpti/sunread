<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class WebsiteListener
{
    public function createCache($website)
    {
        Cache::put("website_{$website->hostname}", $website);
    }

    public function deleteCache($website)
    {
        Cache::forget("website_{$website->hostname}");
    }
}
