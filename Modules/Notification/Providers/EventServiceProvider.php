<?php

namespace Modules\Notification\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Notification\Events\RegistrationSuccess;
use Modules\Notification\Listeners\SendWelcomeEmail;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RegistrationSuccess::class => [
            SendWelcomeEmail::class,
        ],
    ];

    public function boot()
    {
    }
}
