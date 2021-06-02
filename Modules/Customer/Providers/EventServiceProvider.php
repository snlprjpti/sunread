<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Event::listen('customers.session.login.after', 'Modules\Customer\Listeners\SessionListener@customerLogin');
        Event::listen('customers.session.logout.after', 'Modules\Customer\Listeners\SessionListener@customerLogOut');
    }
}
