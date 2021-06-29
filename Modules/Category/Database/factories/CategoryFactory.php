<?php
namespace Modules\Category\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Website;

class CategoryFactory extends Factory
{
    protected $model = \Modules\Category\Entities\Category::class;

    public function definition(): array
    {
        $slug = $this->faker->unique()->slug();

        return [
            "slug" => $slug,
            "position" => $this->faker->randomDigit(),
            "parent_id" => null,
            "website_id" => Website::factory()->create()->id
        ];
    }
}

