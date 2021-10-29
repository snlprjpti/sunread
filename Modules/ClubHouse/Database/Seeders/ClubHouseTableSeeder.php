<?php

namespace Modules\ClubHouse\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\ClubHouse\Entities\ClubHouse;

class ClubHouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClubHouse::insert([
            [
                "type" => "resort",
                "position" => 1,
                "website_id" => 1,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ]);
    }
}
