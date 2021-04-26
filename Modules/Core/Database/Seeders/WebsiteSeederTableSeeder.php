<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WebsiteSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('websites')->insert([
            [
                "code" => "international",
                "hostname" => "international",
                "name" => "International",
                "description" => "For all countries",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
