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
    }
}
