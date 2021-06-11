<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSetTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("attribute_sets")->insert([
            "slug" => "default",
            "name" => "Default",
            "status" => 0,
            "is_user_defined" => 0,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}