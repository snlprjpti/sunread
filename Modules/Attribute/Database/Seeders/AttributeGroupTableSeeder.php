<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeGroupTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('attribute_groups')->delete();

        DB::table('attribute_groups')->insert([
            ['id' => '1','name' => 'General','position' => '1','is_user_defined' => '0','attribute_family_id' => '1','slug'=> 'general'],
            ['id' => '2','name' => 'Description','position' => '2','is_user_defined' => '0','attribute_family_id' => '1','slug'=> 'description'],
            ['id' => '3','name' => 'Meta Description','position' => '3','is_user_defined' => '0','attribute_family_id' => '1','slug'=> 'meta-description'],
            ['id' => '4','name' => 'Price','position' => '4','is_user_defined' => '0','attribute_family_id' => '1','slug'=> 'price'],
            ['id' => '5','name' => 'Shipping','position' => '5','is_user_defined' => '0','attribute_family_id' => '1','slug'=> 'shipping']
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }
}