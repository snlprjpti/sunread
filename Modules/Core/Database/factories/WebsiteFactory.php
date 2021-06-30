<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteFactory extends Factory
{
    protected $model = \Modules\Core\Entities\Website::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $code = $this->faker->unique()->slug();

         return [
            'code' => $code,
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'hostname' => "$code.com",
            "position" => $this->faker->randomDigit()
        ];
    }
}
