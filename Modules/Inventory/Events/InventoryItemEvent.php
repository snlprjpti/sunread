<?php

namespace Modules\Inventory\Events;

use Illuminate\Queue\SerializesModels;

class InventoryItemEvent
{
    use SerializesModels;

    public function __construct(object $catalogInventory, string $event)
    {
        $this->catalogInventory = $catalogInventory;
        $this->event = $event;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
