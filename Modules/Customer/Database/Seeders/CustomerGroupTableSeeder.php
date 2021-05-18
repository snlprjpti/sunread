<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("customer_groups")->insert([
            "slug" => "general",
            "name" => "General",
            "is_user_defined" => 0
        ]);
    }
}