<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeGroupTableSeeder extends Seeder
{
    public function run(): void
    {
        $groups = ["General", "Description", "Meta Description", "Price", "Shipping"];
        $count = 0;
        $data = array_map(function($data) use ($count) {
            return [
                "name" => $data,
                "slug"=> Str::slug($data),
                "position" => ++$count,
                "is_user_defined" => 0,
                "attribute_set_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $groups);

        DB::table("attribute_groups")->insert($data);
    }
}