<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen('storefront.customer.success.registration', 'Modules\Notification\Listeners\NotificationListener@sendEmail');
    }
}
