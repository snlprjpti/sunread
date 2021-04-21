<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert([
           [
               "currency" => "USD",
               "name" => \Str::random(5),
               "slug" => \Str::slug(\Str::random(5)),
               "locale" => \Str::random(2),
           ],
            [
                "currency" => "Euro",
                "name" => \Str::random(5),
                "slug" => \Str::slug(\Str::random(5)),
                "locale" => \Str::random(2),
            ]
        ]);
    }
}
