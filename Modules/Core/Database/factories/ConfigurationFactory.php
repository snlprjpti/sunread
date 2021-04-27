<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ConfigurationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Configuration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $scope=[ "default", "website", "channel", "store" ];

         return [
            'scope' => Arr::random($scope),
            'scope_id' => rand(0,100),
            'path' => $this->faker->name,
            'value' => $this->faker->name
        ];
    }
}


