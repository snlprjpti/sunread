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
            "middle_name" => "Nic",
            "last_name" => "Doe",
            "email" => "customer@example.net",
            "customer_group_id" => 1,
            "website_id" => 1,
            "store_id" => 1,
            "gender" => "male",
            "date_of_birth" => "1950-01-01",
            "password" => bcrypt("password"),
            "profile_image" => null,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
