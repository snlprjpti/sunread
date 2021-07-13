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

        
        $system_defined_attributes = Attribute::whereIsUserDefined(0)->pluck('id')->toArray();
        $user_defined_attributes = Attribute::factory()->create()->id;

        $attribute_group = AttributeGroup::factory()
            ->create([
                "attribute_set_id" => $attribute_set_id,
                "position" => 1
            ])
            ->each(function ($attr_group) use ( $system_defined_attributes, $user_defined_attributes ) {
                $attr_group->attributes()->sync(array_merge($system_defined_attributes, [ $user_defined_attributes ]));
            });

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
