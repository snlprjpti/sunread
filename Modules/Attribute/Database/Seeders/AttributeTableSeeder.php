<?php

namespace Modules\Attribute\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Entities\Attribute;

class AttributeTableSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                "name" => "Slug",
                "type" => "text",
                "is_required" => 1,
                "is_searchable" => 1
            ],
            [
                "name" => "Name",
                "type" => "text",
                "is_required" => 1,
                "is_searchable" => 1
            ],
            [
                "name" => "New",
                "type" => "boolean"
            ],
            [
                "name" => "Featured",
                "type" => "boolean"
            ],
            [
                "name" => "Visible Individually",
                "type" => "boolean",
                "is_required" => 1
            ],
            [
                "name" => "Status",
                "type" => "boolean",
                "is_required" => 1
            ],
            [
                "name" => "Short Description",
                "type" => "textarea",
                "is_required" => 1,
                "is_searchable" => 1
            ],
            [
                "name" => "Description",
                "type" => "textarea",
                "is_required" => 1
            ],
            [
                "name" => "Price",
                "type" => "price",
                "validation" => "decimal",
                "is_required" => 1,
                "use_in_layered_navigation" => 1
            ],
            [
                "name" => "Cost",
                "type" => "price",
                "validation" => "decimal",
                "value_per_channel" => 1,
                "is_user_defined" => 1
            ],
            [
                "name" => "Special Price",
                "type" => "price",
                "validation" => "decimal"
            ],
            [
                "name" => "Special Price From",
                "type" => "date",
                "value_per_channel" => 1
            ],
            [
                "name" => "Special Price To",
                "type" => "date",
                "value_per_channel" => 1
            ],
            [
                "name" => "Meta Title",
                "type" => "textarea"
            ],
            [
                "name" => "Meta Keywords",
                "type" => "textarea"
            ],
            [
                "name" => "Meta Description",
                "type" => "textarea",
                "is_user_defined" => 1
            ],
            [
                "name" => "Width",
                "type" => "text",
                "validation" => "decimal",
                "is_user_defined" => 1
            ],
            [
                "name" => "Height",
                "type" => "text",
                "validation" => "decimal",
                "is_user_defined" => 1
            ],
            [
                "name" => "Depth",
                "type" => "text",
                "validation" => "decimal",
                "is_user_defined" => 1
            ],
            [
                "name" => "Weight",
                "type" => "text",
                "validation" => "decimal",
                "is_required" => 1
            ],
            [
                "name" => "Color",
                "type" => "select",
                "use_in_layered_navigation" => 1,
                "is_configurable" => 1,
                "is_user_defined" => 1
            ],
            [
                "name" => "Size",
                "type" => "select",
                "use_in_layered_navigation" => 1,
                "is_configurable" => 1,
                "is_user_defined" => 1
            ],
        ];

        $count = 0;
        $attributes_array = array_map(function($attribute) use($count) {
            global $count;
            return [
                "slug" => Str::slug($attribute["name"]),
                "name" => $attribute["name"],
                "type" => $attribute["type"],
                "scope" => "global",
                "validation" => $attribute["validation"] ?? NULL,
                "position" => ++$count,
                "is_required" => $attribute["is_required"] ?? 0,
                "use_in_layered_navigation" => $attribute["use_in_layered_navigation"] ?? 1,
                "is_searchable" => $attribute["is_searchable"] ?? 0,
                "is_user_defined" => $attribute["is_user_defined"] ?? 0,
                "is_visible_on_storefront" => 0,
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $attributes);
        DB::table("attributes")->insert($attributes_array);

        $translation_count = 0;
        $attribute_translations_array = array_map(function($attribute) use($translation_count) {
            global $translation_count;
            return [
                "store_id" => 1,
                "name" => $attribute["name"],
                "attribute_id" => ++$translation_count
            ];
        }, $attributes);
        DB::table("attribute_translations")->insert($attribute_translations_array);
    }
}
