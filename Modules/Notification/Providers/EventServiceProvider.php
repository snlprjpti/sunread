<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen("storefront.customer.registration.success", "Modules\Notification\Listeners\NotificationListener@welcomeEmail");
        Event::listen("storefront.customer.confirmation.success", "Modules\Notification\Listeners\NotificationListener@confirmEmail");
        Event::listen("storefront.customer.forgot.password", "Modules\Notification\Listeners\NotificationListener@forgotPassword");
        Event::listen("storefront.customer.reset.password", "Modules\Notification\Listeners\NotificationListener@resetPassword");
    }
}
