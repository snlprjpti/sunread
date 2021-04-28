<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Currency;

class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        while(true) {
            $currency = $this->faker->currencyCode();
            $old_currency = Currency::where("code", $currency)->first();
            if (!$old_currency) break;
        }

        return [
            "code" => $currency,
            "name" => $currency,
            "symbol" => $currency
        ];
    }
}

