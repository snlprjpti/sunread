<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Website::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
         return [
            'code' => \Str::random(16),
            'name' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'hostname' => $this->faker->name,
            "position" => $this->faker->randomDigit()
        ];
    }
}


