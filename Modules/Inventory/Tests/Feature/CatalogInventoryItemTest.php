<?php

namespace Modules\Inventory\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Entities\CatalogInventoryItem;

class CatalogInventoryItemTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = CatalogInventoryItem::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Catalog Inventory Item";
        $this->route_prefix = "admin.catalog.inventory-items";
    }

    public function getCreateData(): array
    {
        $inventory = CatalogInventory::inRandomOrder()->first();
        return $this->model::factory()->make(["catalog_inventories" => [$inventory->id]])->toArray();
    }
    
    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "product_id" => null
        ]);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "event" => null
        ]);
    }
}
