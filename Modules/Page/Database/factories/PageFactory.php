<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = \Modules\Page\Entities\Page::class;

    public function definition(): array
    {
        return [
            "parent_id" => 0,
            "slug" => $this->faker->unique()->slug(),
            "title" => $this->faker->name(),
            "description" => $this->faker->paragraph(),
            "position" => $this->faker->randomDigit(),
            "status" => 1
        ];
    }
}

