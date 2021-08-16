<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen('core.stores.create.after', 'Modules\Core\Listeners\StoreListener@indexing');
        Event::listen('core.stores.update.after', 'Modules\Core\Listeners\StoreListener@indexing');

        Event::listen('core.website.cache.update', 'Modules\Core\Listeners\ResolverListener@updateWebsite');
        Event::listen('core.website.cache.delete', 'Modules\Core\Listeners\ResolverListener@deleteWebsite');

        Event::listen('core.channel.cache.update', 'Modules\Core\Listeners\ResolverListener@updateChannel');
        Event::listen('core.channel.cache.delete', 'Modules\Core\Listeners\ResolverListener@deleteChannel');

        Event::listen('core.store.cache.update', 'Modules\Core\Listeners\ResolverListener@updatestore');
        Event::listen('core.store.cache.delete', 'Modules\Core\Listeners\ResolverListener@deleteStore');

    }
}
