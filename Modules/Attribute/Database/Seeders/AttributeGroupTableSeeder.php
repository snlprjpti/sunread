<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeGroupTableSeeder extends Seeder
{
    /**
     * Insert Attribute Groups
     * 
     * @return Void
     */
    public function run()
    {
        $data = [];
        $groups = ["General", "Description", "Meta Description", "Price", "Shipping"];
        $count = 0;
        foreach ($groups as $name) {
            $data[] = [
                "name" => $name,
                "slug"=> \Str::slug($name),
                "position" => ++$count,
                "is_user_defined" => 0,
                "attribute_family_id" => 1
            ];
        }

        DB::table('attribute_groups')->insert($data);
    }
}