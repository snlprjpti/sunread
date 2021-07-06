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
        CatalogInventory::factory()->create();
    }
}
