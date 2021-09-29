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
        $paths = [ "default_country", "allow_countries", "optional_zip_countries", "general_optional_state", "store_locale", "locale_weight_unit", "global_timezone", "channel_time_zone"];
        $absolute_paths = [ "0.elements.0", "0.elements.1", "0.elements.2", "0.elements.3", "1.elements.0", "1.elements.1", "1.elements.2", "1.elements.3"  ];

        $scope = Arr::random([ "website", "channel", "store" ]);

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

        foreach($paths as $i => $path)
        {
            $items[$path] = [
                'use_default_value' => 1,
                'absolute_path' => "general.children.0.subChildren.{$absolute_paths[$i]}"
            ];
        }
        return [
            "absolute_path" => "general.children.0.subChildren",
            'scope' => $scope,
            'scope_id' => $scope_id,
            'items' => $items
        ];
    }
}
