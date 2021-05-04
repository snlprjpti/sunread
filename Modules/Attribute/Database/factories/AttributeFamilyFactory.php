<?php
namespace Modules\Attribute\Database\factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFamilyFactory extends Factory
{
    protected $model = \Modules\Attribute\Entities\AttributeFamily::class;

    public function definition(): array
    {
        while(true) {
            $name = $this->faker->name();
            $slug = Str::slug($name);
            $old = $this->model::whereSlug($slug)->first();
            if(!$old) break;
        }
        
        return [
            "slug" => $slug,
            "name" => $name
        ];
    }
}

