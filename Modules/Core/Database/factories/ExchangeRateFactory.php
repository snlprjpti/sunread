<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Currency;

class ExchangeRateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\ExchangeRate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $source = Currency::factory()->create();
        $target = Currency::factory()->create();

        return [
            'source_currency' => $source->id,
            'target_currency' => $target->id,
            'rate' => rand(0,10)
        ];
    }
}
