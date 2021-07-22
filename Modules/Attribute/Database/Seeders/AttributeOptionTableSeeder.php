<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeOptionTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("attribute_option_translations")->insert([
            ["store_id" => 1, "attribute_option_id" => 5, "name" => "Red", "code" => "Red"],
            ["store_id" => 1, "attribute_option_id" => 6, "name" => "Green", "code" => "Green"],
            ["store_id" => 1, "attribute_option_id" => 7, "name" => "Yellow", "code" => "Yellow"],
            ["store_id" => 1, "attribute_option_id" => 8, "name" => "Blue", "code" => "Blue"],
            ["store_id" => 1, "attribute_option_id" => 9, "name" => "S", "code" => "S"],
            ["store_id" => 1, "attribute_option_id" => 10, "name" => "M", "code" => "M"],
            ["store_id" => 1, "attribute_option_id" => 11, "name" => "L", "code" => "L"],
            ["store_id" => 1, "attribute_option_id" => 12, "name" => "XL", "code" => "XL"]
        ]);
    }
}
