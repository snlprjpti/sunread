<?php

namespace Modules\Sales\Listeners;

use Modules\Sales\Events\OrderStatusUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param OrderStatusUpdated $event
     * @return void
     */
    public function handle(OrderStatusUpdated $event)
    {
        //
    }
}
