<?php

namespace Modules\Attribute\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen('catalog.attribute.update.after', 'Modules\Attribute\Listeners\AttributeListener@indexing');
    }
}
