<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;

class ProductTableSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::factory()
            ->create([
                "parent_id" => Product::factory()->configurable()->create()->id,
                "attribute_group_id" => 1
            ]);
            DB::table('product_attributes')->insert([
                [
                    "product_id" => $product->id,
                    "attribute_id" => 1,
                    "value_type" => "Modules\Product\Entities\ProductAttributeString",
                    "value_id" => null,
                    "store_id" => 1,
                    "channel_id" => null
                ]
            ]);
    }
}
