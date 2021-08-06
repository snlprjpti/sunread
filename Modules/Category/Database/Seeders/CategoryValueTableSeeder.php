<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
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
        $data = [
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "name",
                "value" => "Root"
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "slug",
                "value" => "root"
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "status",
                "value" => 1
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "include_in_menu",
                "value" => 1
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "description",
                "value" => "Good"
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "image",
                "value" => null
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "meta_title",
                "value" => "Root"
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "meta_description",
                "value" => "Root"
            ],
            [
                "category_id" => 1,
                "scope" => "website",
                "scope_id" => 1,
                "attribute" => "meta_keywords",
                "value" => "Root"
            ],
        ];

        $data = array_map(function($item) {
            return array_merge($item, [
                "value" => json_encode($item["value"]),
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }, $data);

        CategoryValue::insert($data);
    }
}
