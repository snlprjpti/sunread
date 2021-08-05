<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;

class CategoryValueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::inRandomOrder()->first();
        dd($category);
        $category_values = [
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
                "attribute" => "slug",
                "value" => "root",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "status",
                "value" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "include_in_menu",
                "value" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "description",
                "value" => "Good",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "image",
                "value" => null,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "meta_title",
                "value" => "Root",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "meta_description",
                "value" => "Root",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "meta_keywords",
                "value" => "Root",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];
        foreach($category_values as $category_value)
        {
            CategoryValue::create($category_value);
        }
    }
}
