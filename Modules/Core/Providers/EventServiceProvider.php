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

        Event::listen('core.website.update.after', 'Modules\Core\Listeners\Resolver\WebsiteListener@updateWebsite');
        Event::listen('core.website.delete.after', 'Modules\Core\Listeners\Resolver\WebsiteListener@deleteWebsite');

        Event::listen('core.channel.update.after', 'Modules\Core\Listeners\Resolver\ChannelListener@updateChannel');
        Event::listen('core.channel.delete.after', 'Modules\Core\Listeners\Resolver\ChannelListener@deleteChannel');

        Event::listen('core.store.update.after', 'Modules\Core\Listeners\Resolver\StoreListener@updatestore');
        Event::listen('core.store.delete.after', 'Modules\Core\Listeners\Resolver\StoreListener@deleteStore');
    }
}
