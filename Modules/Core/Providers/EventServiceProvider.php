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

        Event::listen('core.website.create.after', 'Modules\Core\Listeners\Resolver\WebsiteListener@createCache');
        Event::listen('core.website.update.after', 'Modules\Core\Listeners\Resolver\WebsiteListener@updateCache');
        Event::listen('core.website.delete.after', 'Modules\Core\Listeners\Resolver\WebsiteListener@deleteCache');

        Event::listen('core.channel.create.after', 'Modules\Core\Listeners\Resolver\ChannelListener@createCache');
        Event::listen('core.channel.update.after', 'Modules\Core\Listeners\Resolver\ChannelListener@updateCache');
        Event::listen('core.channel.delete.after', 'Modules\Core\Listeners\Resolver\ChannelListener@deleteCache');

        Event::listen('core.stores.create.after', 'Modules\Core\Listeners\Resolver\StoreListener@createCache');
        Event::listen('core.stores.update.after', 'Modules\Core\Listeners\Resolver\StoreListener@updateCache');
        Event::listen('core.stores.delete.after', 'Modules\Core\Listeners\Resolver\StoreListener@deleteCache');
    }
}
