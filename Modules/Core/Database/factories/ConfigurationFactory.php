<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Configuration;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class ConfigurationFactory extends Factory
{
    protected $model = \Modules\Core\Entities\Configuration::class;

    public function definition(): array
    {
        $scope = Arr::random([ "default", "website", "channel", "store" ]);
        $path = Arr::random([ "default_country", "allow_countries", "optional_zip_countries" ]);
        $scope_id = 0;

        switch ($scope) {
            case "website":
                $scope_id = Website::factory()->create()->id;
                break;

            case "channel":
                $scope_id = Channel::factory()->create()->id;
                break;

            case "store":
                $scope_id = Store::factory()->create()->id;
                break;
        }

        return [
            'scope' => $scope,
            'scope_id' => $scope_id,
            'items' => [
                $path => $this->faker->name()
            ]
        ];
    }
}
