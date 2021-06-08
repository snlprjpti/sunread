<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Attribute\Entities\Attribute;
use Modules\Core\Entities\Store;
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

            $attribute_types = config("attribute_types");
            unset($attribute_types["image"], $attribute_types["file"]);
 
            for($i = 0; $i < 4; $i++)
            {
                $attribute_type = array_rand($attribute_types);
                $attribute_model = $attribute_types[$attribute_type];
                ProductAttribute::create([
                    "product_id" => $product->id,
                    "attribute_id" => Attribute::factory()->create(["type" => $attribute_type])->id,
                    "value_type" => $attribute_model,
                    "value_id" => $attribute_model::factory()->create()->id,
                    "store_id" => Store::factory()->create()->id,
                    "channel_id" => null
                ]);
            }

    }
}
