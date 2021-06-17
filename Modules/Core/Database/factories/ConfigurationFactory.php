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
        $paths = [ "default_country", "allow_countries", "optional_zip_countries" ];
        $absolute_paths = [ "general.children.0.subChildren.0.elements.0", "general.children.0.subChildren.0.elements.1", "general.children.0.subChildren.0.elements.2" ];
        $i = rand(0,2);
        $scope_id = 0;
        $scope = Arr::random([ "global", "website", "channel", "store" ]);

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
                $paths[$i] => [
                    'value' => $this->faker->name(),
                    'absolute_path' => $absolute_paths[$i]
                ] 
            ]
        ];
    }
}
