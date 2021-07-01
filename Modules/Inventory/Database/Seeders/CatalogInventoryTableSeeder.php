<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CatalogInventoryTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("catalog_inventories")->insert([
            "product_id" => 1,
            "website_id" => 1,
            "quantity" => 10,
            "is_in_stock" => 1,
            "manage_stock" => 1,
            "use_config_manage_stock" => 1,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
