<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeFamilyTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("attribute_families")->insert([
            "slug" => "default",
            "name" => "Default",
            "status" => 0,
            "is_user_defined" => 0,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}