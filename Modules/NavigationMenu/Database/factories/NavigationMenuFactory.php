<?php
namespace Modules\NavigationMenu\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class NavigationMenuFactory extends Factory
{
    protected $model = \Modules\NavigationMenu\Entities\NavigationMenu::class;

    public function definition(): array
    {
        $location = ["footer", "primary", "full_screen"];
        return [
            "title" => $this->faker->name(),
            "slug" => $this->faker->unique()->slug(),
            "status" => 1,
            "location" => Arr::random($location)
        ];
    }
}

