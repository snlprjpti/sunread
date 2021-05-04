<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::table('channels')->insert([
            [
                "default_store_id" => 1,
                "default_currency" => "USD",
                "code" => "international",
                "hostname" => "international",
                "name" => "International",
                "location" => "International",
                "description" => "For all countries",
                "website_id" => 1,
                "timezone" => "UTC",
                "theme" => "default",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
