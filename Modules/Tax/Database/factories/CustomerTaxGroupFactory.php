<?php
namespace Modules\Tax\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerTaxGroupFactory extends Factory
{
    protected $model = \Modules\Tax\Entities\CustomerTaxGroup::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->name(),
            "description" => $this->faker->sentence()
        ];
    }
}
