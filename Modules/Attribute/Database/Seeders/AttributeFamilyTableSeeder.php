<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeFamilyTableSeeder extends Seeder
{
    /**
     * Insert Attribute Family
     * 
     * @return Void
     */
    public function run()
    {
        DB::table('attribute_families')->insert([
            "slug" => "default",
            "name" => "Default",
            "status" => 0,
            "is_user_defined" => 1
        ]);
    }
}