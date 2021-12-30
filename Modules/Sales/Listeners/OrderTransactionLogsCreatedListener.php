<?php

namespace Modules\Sales\Listeners;

use Modules\Sales\Events\OrderTransactionLogsCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderTransactionLogsCreatedListener
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
     * @param OrderTransactionLogsCreated $event
     * @return void
     */
    public function handle(OrderTransactionLogsCreated $event)
    {
        //
    }
}
