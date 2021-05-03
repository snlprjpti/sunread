<?php
namespace Modules\Attribute\Database\factories;

use Illuminate\Support\Str;
use Modules\Attribute\Entities\AttributeFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeGroupFactory extends Factory
{
    protected $model = \Modules\Attribute\Entities\AttributeGroup::class;

    public function definition(): array
    {
        while(true) {
            $name = $this->faker->name();
            $slug = Str::slug($name);
            $old = $this->model::whereSlug($slug)->first();
            if(!$old) break;
        }
        
        return [
            "attribute_family_id" => AttributeFamily::factory()->create()->id,
            "slug" => $slug,
            "name" => $name,
            "position" => rand(1,20)
        ];
    }
}

