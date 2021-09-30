<?php

namespace Modules\Notification\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Notification\Events\ConfirmEmail;
use Modules\Notification\Events\ForgotPassword;
use Modules\Notification\Events\RegistrationSuccess;
use Modules\Notification\Events\ResetPassword;
use Modules\Notification\Listeners\SendAccountConfirmation;
use Modules\Notification\Listeners\SendPasswordChangeEmail;
use Modules\Notification\Listeners\SendPasswordResetLink;
use Modules\Notification\Listeners\SendWelcomeEmail;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RegistrationSuccess::class => [
            SendWelcomeEmail::class,
        ],
        ConfirmEmail::class => [
            SendAccountConfirmation::class,
        ],
        ForgotPassword::class => [
            SendPasswordResetLink::class,
        ],
        ResetPassword::class => [
            SendPasswordChangeEmail::class,
        ],
    ];

    public function boot()
    {
    }
}
