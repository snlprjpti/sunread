<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\ProductAttribute;

class ProductAttributeTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_attributes')->insert([
            [
                "product_id" => 1,
                "attribute_id" => 1,
                "value_type" => "Modules\Product\Entities\ProductAttributeString",
                "value_id" => null,
                "store_id" => 1,
                "channel_id" => null
            ]
        ]);
    }
}
