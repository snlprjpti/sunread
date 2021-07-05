<?php

namespace Modules\Inventory\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Inventory\Entities\CatalogInventory;

class CatalogInventoryTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = CatalogInventory::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Catalog Inventory";
        $this->route_prefix = "admin.catalog.inventories";
    }

    public function getCreateData(): array
    {
        return $this->model::factory()->make(["is_in_stock" => true])->toArray();
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "product_id" => null
        ]);
    }
}
