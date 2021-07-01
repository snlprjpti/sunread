<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CatalogInventoryItemTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("catalog_inventory_items")->insert([
            "product_id" => 1,
            "order_id" => 1,
            "event" => "Manual Adjustment",
            "adjusted_by" => 1,
            "adjustment_type" => "addition",
            "quantity" => 5,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
