<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Events\InventoryItemEvent;
use Modules\Inventory\Listeners\InventoryItemListener;

class EventServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        Event::listen(InventoryItemEvent::class, [
            InventoryItemListener::class, "handle"
        ]);
    }

    public function provides(): array
    {
        return [];
    }
}
