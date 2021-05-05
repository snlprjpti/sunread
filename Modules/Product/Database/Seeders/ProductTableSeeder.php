<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Entities\Product;

class ProductTableSeeder extends Seeder
{
    public function run(): void
    {
        // Seed 10 fake products
        Product::factory(10)->create([
            "attribute_group_id" => 1
        ]);
    }
}
