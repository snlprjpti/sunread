<?php
namespace Modules\Category\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = \Modules\Category\Entities\Category::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $slug = $this->faker->unique()->slug();

        return [
            "slug" => $slug,
            "name" => $name,
            "position" => $this->faker->randomDigit(),
            "status" => 1 
        ];
    }
}

