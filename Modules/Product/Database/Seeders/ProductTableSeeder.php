<?php

namespace Modules\Product\Database\Seeders;

use Attribute;
use Illuminate\Database\Seeder;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;

class ProductTableSeeder extends Seeder
{
    public function run(): void
    {
        // Seed 10 fake products
        Product::factory(10)
            ->has(ProductAttribute::factory(rand(5, 15)), 'product_attributes')
            ->create([
                "parent_id" => Product::factory()->configurable()->create()->id,
                "attribute_group_id" => 1
            ]);
    }
}
