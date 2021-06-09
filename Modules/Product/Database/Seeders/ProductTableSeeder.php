<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;

class ProductTableSeeder extends Seeder
{
    public function run(): void
    {
        Product::withoutSyncingToSearch(function () {
            Product::factory()
                ->has(ProductAttribute::factory(), 'product_attributes')
                ->create([
                    "parent_id" => Product::factory()->configurable()->create()->id,
                    "attribute_group_id" => 1
                ]);
        });
    }
}
