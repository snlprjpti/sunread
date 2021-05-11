<?php
namespace Modules\Attribute\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFamilyFactory extends Factory
{
    protected $model = \Modules\Attribute\Entities\AttributeFamily::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $slug = $this->faker->unique()->slug();
    
        return [
            "slug" => $slug,
            "name" => $name
        ];
    }
}

