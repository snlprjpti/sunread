<?php

namespace Modules\Inventory\Observers;

use Modules\Core\Facades\Audit;
use Modules\Inventory\Entities\CatalogInventoryItem;

class CatalogInventoryItemObserver
{
    public function created(CatalogInventoryItem $catalogInventoryItem)
    {
        Audit::log($catalogInventoryItem, __FUNCTION__);
    }

    public function updated(CatalogInventoryItem $catalogInventoryItem)
    {
        Audit::log($catalogInventoryItem, __FUNCTION__);
    }

    public function deleted(CatalogInventoryItem $catalogInventoryItem)
    {
        Audit::log($catalogInventoryItem, __FUNCTION__);
    }

}
