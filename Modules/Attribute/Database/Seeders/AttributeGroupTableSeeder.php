<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Entities\AttributeGroup;

class AttributeGroupTableSeeder extends Seeder
{
    public function run(): void
    {
        $groups = ["Product Details", "Content", "Search Engine Optimization", "Animated Image", "Product Images", "Product Builder", "Technical Details"];
        $attribute_group_attributes = [
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 22, 23, 24, 27, 29],
            [16, 17, 25, 26],
            [18, 19, 20, 21],
            [31, 32],
            [28],
            [30],
            [33, 34, 35, 36, 37, 38, 39, 40]
        ];
        foreach($groups as $count => $data)
        {
            $attribute_group_data = [
                "name" => $data,
                "position" => $count + 1,
                "attribute_set_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ];
            $attribute_group = AttributeGroup::create($attribute_group_data);
            $attribute_group->attributes()->sync($attribute_group_attributes[$count]);
        }
    }
}