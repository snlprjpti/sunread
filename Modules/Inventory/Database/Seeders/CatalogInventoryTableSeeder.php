<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\CatalogInventoryItem;
use Modules\Inventory\Entities\CatalogInventory;

class CatalogInventoryTableSeeder extends Seeder
{
    public function run(): void
    {
        CatalogInventory::withoutEvents(function () {
            CatalogInventory::factory()
            ->create()
            ->each(function ($catalog_inventory){
                $catalog_inventory->catalog_inventory_items()->attach(CatalogInventoryItem::factory(2)->create());
            });
        });

    }
}
