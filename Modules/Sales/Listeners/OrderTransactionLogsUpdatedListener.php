<?php

namespace Modules\Sales\Listeners;

use Modules\Sales\Events\OrderTransactionLogsUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderTransactionLogsUpdatedListener
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
     * @param OrderTransactionLogsUpdated $event
     * @return void
     */
    public function handle(OrderTransactionLogsUpdated $event)
    {
        //
    }
}
