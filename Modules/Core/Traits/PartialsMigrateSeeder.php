<?php

namespace Modules\Core\Traits;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeGroup;
use Illuminate\Support\Str;
use Modules\Attribute\Entities\AttributeTranslation;

trait PartialsMigrateSeeder
{
    public function updateAttributes(): bool
    {
        try
        {
            $attribute_model = new Attribute();

            // updated image attributes types
            $base_image = $attribute_model->where("slug", "base_image")->first();
            $base_image->update(["type" => "multiimages"]);
            $small_image = $attribute_model->where("slug", "small_image")->first();
            $small_image->update(["type" => "multiimages"]);
            $thumbnail_image = $attribute_model->where("slug", "thumbnail_image")->first();
            $thumbnail_image->update(["type" => "multiimages"]);

            // Added attributes
            $attributes = [
                [
                    "name" => "Section Background Image",
                    "slug" => "section_background_image",
                    "type" => "image",
                    "scope" => "website",
                    "is_required" => 0
                ],
                [
                    "name" => "Gallery",
                    "slug" => "gallery",
                    "type" => "multiimages",
                    "scope" => "website",
                    "is_required" => 0
                ],
            ];

            array_map(function ($attribute) use ($attribute_model) {
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
    
                $attribute_data = Attribute::withoutEvents( function () use ($attribute_array, $attribute_model) {
                    return $attribute_model::updateOrCreate($attribute_array);
                });
    
                AttributeTranslation::updateOrCreate([
                    "store_id" => 1,
                    "name" => $attribute["name"],
                    "attribute_id" => $attribute_data->id
                ]);
    
            }, $attributes);
                        
            $gallery = $attribute_model->where("slug", "gallery")->first();
            $section_background_image = $attribute_model->where("slug", "section_background_image")->first();

            // Product images attribute group id 4
            $attribute_group = AttributeGroup::whereId(4)->first();
            // removed images attributes
            $attribute_group->attributes()->detach([$base_image->id, $small_image->id, $thumbnail_image->id, $section_background_image->id, $gallery->id]);
            $attribute_group->attributes()->attach($gallery->id);

        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }
} 