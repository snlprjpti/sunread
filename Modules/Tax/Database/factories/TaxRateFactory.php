<?php
namespace Modules\Tax\Database\factories;

use Modules\Country\Entities\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxRateFactory extends Factory
{
    protected $model = \Modules\Tax\Entities\TaxRate::class;

    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();
        $country_id = $country->id;
        $region_id = $country->regions()->inRandomOrder()->first()?->id;

        return [
            "country_id" => $country_id,
            "region_id" => $region_id,
            "identifier" => $this->faker->unique()->slug(),
            "use_zip_range" => 0,
            "zip_code" => "*",
            "postal_code_from" => null,
            "postal_code_to" => null,
            "tax_rate" => $this->faker->randomFloat(2, min: 5, max: 20)
        ];
    }
}

