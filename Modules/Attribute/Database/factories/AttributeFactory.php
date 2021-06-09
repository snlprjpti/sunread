<?php
namespace Modules\Attribute\Database\factories;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Core\Entities\Store;

class AttributeFactory extends Factory
{
    protected $model = \Modules\Attribute\Entities\Attribute::class;

    public function definition(): array
    {
        while(true) {
            $name = $this->faker->name();
            $slug = Str::slug($name);
            $old_attribute = $this->model::whereSlug($slug)->first();
            if(!$old_attribute) break;
        }

        $type = Arr::random([
            "text",
            "textarea",
            "price",
            "boolean",
            "select",
            "multiselect",
            "datetime",
            "date",
            "image",
            "file",
            "checkbox"
        ]);

        return [
            "slug" => $slug,
            "name" => $name,
            "type" => $type,
            "position" => rand(1,20),
            "is_required" => rand(0,1),
            "is_unique" => rand(0,1),
            "validation" => null,
            "is_visible_on_front" => rand(0,1),
            "attribute_group_id" => AttributeGroup::factory()->create()->id
        ];
    }
}

