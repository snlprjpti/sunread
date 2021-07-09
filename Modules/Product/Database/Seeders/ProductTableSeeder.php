<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\Attribute;

class ProductTableSeeder extends Seeder
{
    public function run(): void
    {
        $attribute_set_id = AttributeSet::factory()->create()->id;
        $attribute_group = AttributeGroup::factory(1)
            ->create([
                "attribute_set_id" => $attribute_set_id,
                "position" => 1
            ])
            ->each(function ($attr_group){
                $attr_group->attributes()->attach(Attribute::factory(1)->create());
            })->first();

        Product::withoutSyncingToSearch(function () use ($attribute_set_id){
            Product::factory()
                ->has(ProductAttribute::factory(), 'product_attributes')
                ->create([
                    "parent_id" => Product::factory()->configurable()->create()->id,
                    "attribute_set_id" => $attribute_set_id
                ]);
        });
    }
}
