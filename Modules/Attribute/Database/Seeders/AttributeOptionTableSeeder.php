<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeOptionTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('attribute_options')->delete();
        DB::table('attribute_option_translations')->delete();

        DB::table('attribute_options')->insert([
            ['id' => '1', 'name' => 'Red', 'position' => '1', 'attribute_id' => '23'],
            ['id' => '2', 'name' => 'Green', 'position' => '2', 'attribute_id' => '23'],
            ['id' => '3', 'name' => 'Yellow', 'position' => '3', 'attribute_id' => '23'],
            ['id' => '4', 'name' => 'Black', 'position' => '4', 'attribute_id' => '23'],
            ['id' => '5', 'name' => 'White', 'position' => '5', 'attribute_id' => '23'],
            ['id' => '6', 'name' => 'S', 'position' => '1', 'attribute_id' => '24'],
            ['id' => '7', 'name' => 'M', 'position' => '2', 'attribute_id' => '24'],
            ['id' => '8', 'name' => 'L', 'position' => '3', 'attribute_id' => '24'],
            ['id' => '9', 'name' => 'XL', 'position' => '4', 'attribute_id' => '24']
        ]);

        DB::table('attribute_option_translations')->insert([
            ['id' => '1', 'locale' => 'en', 'name' => 'Red', 'attribute_option_id' => '1'],
            ['id' => '2', 'locale' => 'en', 'name' => 'Green', 'attribute_option_id' => '2'],
            ['id' => '3', 'locale' => 'en', 'name' => 'Yellow', 'attribute_option_id' => '3'],
            ['id' => '4', 'locale' => 'en', 'name' => 'Black', 'attribute_option_id' => '4'],
            ['id' => '5', 'locale' => 'en', 'name' => 'White', 'attribute_option_id' => '5'],
            ['id' => '6', 'locale' => 'en', 'name' => 'S', 'attribute_option_id' => '6'],
            ['id' => '7', 'locale' => 'en', 'name' => 'M', 'attribute_option_id' => '7'],
            ['id' => '8', 'locale' => 'en', 'name' => 'L', 'attribute_option_id' => '8'],
            ['id' => '9', 'locale' => 'en', 'name' => 'XL', 'attribute_option_id' => '9']
        ]);
    }
}