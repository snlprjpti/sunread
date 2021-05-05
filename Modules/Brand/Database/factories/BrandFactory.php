<?php
namespace Modules\Brand\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Brand\Entities\Brand;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Brand\Entities\Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [   
            "name" => $this->faker->name(),
            "slug" => $this->faker->slug(),
            "description" => $this->faker->paragraph
        ];
    }
}

