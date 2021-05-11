<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("customers")->insert([
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "customer@example.net",
            "password" => bcrypt("password")
        ]);
    }
}
