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
                "attribute" => "name",
                "value" => "Root",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "description",
                "value" => null,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);
    }
}
