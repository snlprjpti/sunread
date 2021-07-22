<?php

namespace Modules\Inventory\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Inventory\Entities\CatalogInventoryItem;

class CatalogInventoryItemRepository extends BaseRepository
{
    public function __construct(CatalogInventoryItem $catalogInventoryItem)
    {
        $this->model = $catalogInventoryItem;
        $this->model_key = "Catalog Inventory Item";
        $this->rules = [];
    }
}
