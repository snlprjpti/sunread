<?php

namespace Modules\Attribute\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Attribute\Entities\AttributeTranslation;

class AttributeTableSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                "name" => "Product Name",
                "slug" => "name",
                "type" => "text",
                "is_required" => 1,
                "is_searchable" => 1,
                "search_weight" => 6,
                "scope" => "store"
            ],
            [
                "name" => "SKU",
                "type" => "text",
                "is_required" => 1,
                "is_searchable" => 1,
                "search_weight" => 6,
                "is_unique" => 1,
                "is_visible_on_storefront" => 1,
                "comparable_on_storefront" => 1,
                "scope" => "website"
            ],
            // [
            //     "name" => "New",
            //     "type" => "boolean",
            //     "default_value" => 0
            // ],
            // [
            //     "name" => "Featured",
            //     "type" => "boolean",
            //     "default_value" => 0
            // ],
            [
                "name" => "Price",
                "type" => "price",
                "validation" => "decimal",
                "is_required" => 1,
                "use_in_layered_navigation" => 1,
                "scope" => "channel"
            ],
            [
                "name" => "Cost",
                "type" => "price",
                "validation" => "decimal",
                "is_required" => 1,
                "use_in_layered_navigation" => 1,
                "scope" => "channel"
            ],
            [
                "name" => "Special Price",
                "slug" => "special_price",
                "type" => "price",
                "validation" => "decimal",
                "scope" => "channel"
            ],
            [
                "name" => "Special Price From Date",
                "slug" => "special_from_date",
                "type" => "date",
                "scope" => "channel"
            ],
            [
                "name" => "Special Price To Date",
                "slug" => "special_to_date",
                "type" => "date",
                "scope" => "channel"
            ],
            [
                "name" => "Quantity And Stock Status",
                "slug" => "quantity_and_stock_status",
                "type" => "select",
                "options" => ["In stock", "Out of stock"],
                "default_value" => "In stock",
                "scope" => "website"
            ],
            [
                "name" => "Has Weight",
                "slug" => "has_weight",
                "type" => "boolean",
                "default_value" => 1,
                "scope" => "website"
            ],
            // [
            //     "name" => "Width",
            //     "type" => "text",
            //     "validation" => "decimal",
            //     "is_user_defined" => 1
            // ],
            // [
            //     "name" => "Height",
            //     "type" => "text",
            //     "validation" => "decimal",
            //     "is_user_defined" => 1
            // ],
            // [
            //     "name" => "Depth",
            //     "type" => "text",
            //     "validation" => "decimal",
            //     "is_user_defined" => 1
            // ],
            [
                "name" => "Weight",
                "type" => "text",
                "validation" => "decimal",
                "scope" => "website"
            ],
            [
                "name" => "Visibility",
                "type" => "select",
                "is_required" => 1,
                "default_value" => "Not Visible Individually",
                "options" => ["Not Visible Individually", "Catalog", "Search", "Catalog, Search"],
                "scope" => "store"
            ],
            [
                "name" => "Tax Class",
                "slug" => "tax_class_id",
                "type" => "select",
                "is_required" => 1,
                "default_value" => "Taxable Goods",
                "options" => [],
                "scope" => "channel"
            ],
            [
                "name" => "Categories",
                "slug" => "category_ids",
                "type" => "multiselect",
                "scope" => "website"
            ],
            [
                "name" => "Set Product as New from Date",
                "slug" => "new_from_date",
                "type" => "date",
                "scope" => "channel"
            ],
            [
                "name" => "Set Product as New to Date",
                "slug" => "new_to_date",
                "type" => "date",
                "scope" => "channel"
            ],
            [
                "name" => "Description",
                "type" => "texteditor",
                "scope" => "store",
                "is_searchable" => 1,
                "search_weight" => 1
            ],
            [
                "name" => "Short Description",
                "slug" => "short_description",
                "type" => "texteditor",
                "scope" => "store",
                "is_searchable" => 1,
                "search_weight" => 1
            ],
            [
                "name" => "URL key",
                "slug" => "url_key",
                "type" => "text",
                "scope" => "store",
                "is_required" => 1,
                "is_searchable" => 1,
                "search_weight" => 1
            ],
            [
                "name" => "Meta Keywords",
                "slug" => "meta_keywords",
                "type" => "textarea",
                "scope" => "store",
                "is_required" => 1
            ],
            [
                "name" => "Meta Title",
                "slug" => "meta_title",
                "type" => "text",
                "scope" => "store",
                "is_required" => 1
            ],
            [
                "name" => "Meta Description",
                "slug" => "meta_description",
                "type" => "textarea",
                "scope" => "store",
                "is_required" => 1
            ],
            [
                "name" => "Product Status",
                "slug" => "status",
                "type" => "boolean",
                "scope" => "website",
                "is_required" => 1,
                "default_value" => 1
            ],
            [
                "name" => "Base Image",
                "slug" => "base_image",
                "type" => "multiselect",
                "scope" => "website",
                "is_required" => 0
            ],
            [
                "name" => "Small Image",
                "slug" => "small_image",
                "type" => "multiselect",
                "scope" => "website",
                "is_required" => 0
            ],
            [
                "name" => "Thumbnail Image",
                "slug" => "thumbnail_image",
                "type" => "multiselect",
                "scope" => "website",
                "is_required" => 0
            ],
            [
                "name" => "Color",
                "type" => "select",
                "use_in_layered_navigation" => 1,
                "default_value" => "Red",
                "options" => ["Red", "Green", "Yellow", "Blue"],
            ],
            [
                "name" => "Size",
                "type" => "select",
                "use_in_layered_navigation" => 1,
                "default_value" => "S",
                "options" => ["S", "M", "L", "XL"],
            ],
        ];

        array_map(function($attribute){
            $default_value = isset($attribute["default_value"]) && !in_array($attribute["type"], ["select", "multiselect", "checkbox"]) ? $attribute["default_value"] : null;
            $attribute_array = [
                "slug" => $attribute["slug"] ?? Str::slug($attribute["name"]),
                "name" => $attribute["name"],
                "type" => $attribute["type"],
                "scope" => $attribute["scope"] ?? "website",
                "validation" => $attribute["validation"] ?? null,
                "is_required" => $attribute["is_required"] ?? 0,
                "is_unique" => $attribute["is_unique"] ?? 0,
                "use_in_layered_navigation" => $attribute["use_in_layered_navigation"] ?? 0,
                "comparable_on_storefront" => $attribute["comparable_on_storefront"] ?? 0,
                "is_searchable" => $attribute["is_searchable"] ?? 0,
                "search_weight" => $attribute["search_weight"] ?? null,
                "is_user_defined" => $attribute["is_user_defined"] ?? 0,
                "is_visible_on_storefront" => $attribute["is_visible_on_storefront"] ?? 0,
                "default_value" => $default_value
            ];

            $attribute_data = Attribute::withoutEvents( function () use ($attribute_array) {
                return Attribute::create($attribute_array);
            });

            AttributeTranslation::create([
                "store_id" => 1,
                "name" => $attribute["name"],
                "attribute_id" => $attribute_data->id
            ]);

            if(isset($attribute["options"])) 
            {
                $count = 0;
                array_map(function($attribute_option) use($attribute_data, $attribute, $count) {
                    global $count;
                    AttributeOption::withoutEvents( function () use ( $attribute_data, $count, $attribute_option, $attribute ) {
                        AttributeOption::create([
                            "attribute_id" => $attribute_data->id,
                            "name" => $attribute_option,
                            "position" => ++$count,
                            "is_default" => ( $attribute["default_value"] == $attribute_option ) ? 1 : 0
                        ]);
                    });
                }, $attribute["options"]);
            }

        }, $attributes);
    }
}
