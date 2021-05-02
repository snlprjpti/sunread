<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Event::listen('customer.session.login.after', 'Modules\Customer\Listeners\SessionListener@customerLogin');
        Event::listen('customer.session.logout.after', 'Modules\Customer\Listeners\SessionListener@customerLogOut');
    }
}
