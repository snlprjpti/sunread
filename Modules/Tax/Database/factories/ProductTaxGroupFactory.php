<?php
namespace Modules\Tax\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTaxGroupFactory extends Factory
{
    protected $model = \Modules\Tax\Entities\ProductTaxGroup::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->name(),
            "description" => $this->faker->sentence()
        ];
    }
}
