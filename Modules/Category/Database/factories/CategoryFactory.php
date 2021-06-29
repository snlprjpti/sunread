<?php
namespace Modules\Category\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Entities\Category;

class CategoryFactory extends Factory
{
    protected $model = \Modules\Category\Entities\Category::class;

    public function definition(): array
    {
        $slug = $this->faker->unique()->slug();
        $root_category = Category::inRandomOrder()->first();

        return [
            "slug" => $slug,
            "position" => $this->faker->randomDigit(),
            "parent_id" => $root_category->id,
            "website_id" => $root_category->website_id
        ];
    }
}

