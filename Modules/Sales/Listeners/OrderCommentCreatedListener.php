<?php

namespace Modules\Sales\Listeners;

use Modules\Sales\Events\OrderCommentCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCommentCreatedListener
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
     * @param OrderCommentCreated $event
     * @return void
     */
    public function handle(OrderCommentCreated $event)
    {
        //
    }
}
