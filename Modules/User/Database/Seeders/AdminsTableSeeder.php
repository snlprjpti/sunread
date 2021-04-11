<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table("admins")->insert([
            "first_name" => "Admin",
            "last_name" => "example",
            "email" => "admin@example.com",
            "password" => bcrypt("admin123"),
            "api_token" => Str::random(80),
            "status" => 1,
            "role_id" => 1,
            "company" => "abc.co",
            "address" => "sweden",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
