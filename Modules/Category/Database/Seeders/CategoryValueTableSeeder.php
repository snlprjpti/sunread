<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryValueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_values')->insert([
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "name" => "Root",
                "image" => NULL,
                "status" => 1,
                "include_in_menu" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
