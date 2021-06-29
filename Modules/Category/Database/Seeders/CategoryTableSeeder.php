<?php

namespace Modules\Category\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("categories")->insert([
            [
                "parent_id" => NULL,
                "_lft" => 1,
                "_rgt" => 14,
                "slug" => "root",
                "position" => 1,
                "website_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "parent_id" => NULL,
                "_lft" => 15,
                "_rgt" => 30,
                "slug" => "root",
                "position" => 2,
                "website_id" => 2,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
