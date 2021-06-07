<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeOptionTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("attribute_options")->insert([
            ["position" => 1, "attribute_id" => 21, "created_at" => now(), "updated_at" => now(), "name" => "Red"],
            ["position" => 2, "attribute_id" => 21, "created_at" => now(), "updated_at" => now(), "name" => "Green"],
            ["position" => 3, "attribute_id" => 21, "created_at" => now(), "updated_at" => now(), "name" => "Yellow"],
            ["position" => 4, "attribute_id" => 21, "created_at" => now(), "updated_at" => now(), "name" => "Black"],
            ["position" => 5, "attribute_id" => 21, "created_at" => now(), "updated_at" => now(), "name" => "White"],
            ["position" => 1, "attribute_id" => 22, "created_at" => now(), "updated_at" => now(), "name" => "S"],
            ["position" => 2, "attribute_id" => 22, "created_at" => now(), "updated_at" => now(), "name" => "M"],
            ["position" => 3, "attribute_id" => 22, "created_at" => now(), "updated_at" => now(), "name" => "L"],
            ["position" => 4, "attribute_id" => 22, "created_at" => now(), "updated_at" => now(), "name" => "XL"]
        ]);

        DB::table("attribute_option_translations")->insert([
            ["store_id" => 1, "attribute_option_id" => 1, "name" => "Red"],
            ["store_id" => 1, "attribute_option_id" => 2, "name" => "Green"],
            ["store_id" => 1, "attribute_option_id" => 3, "name" => "Yellow"],
            ["store_id" => 1, "attribute_option_id" => 4, "name" => "Black"],
            ["store_id" => 1, "attribute_option_id" => 5, "name" => "White"],
            ["store_id" => 1, "attribute_option_id" => 6, "name" => "S"],
            ["store_id" => 1, "attribute_option_id" => 7, "name" => "M"],
            ["store_id" => 1, "attribute_option_id" => 8, "name" => "L"],
            ["store_id" => 1, "attribute_option_id" => 9, "name" => "XL"]
        ]);
    }
}