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
        $website = Website::factory()->create();
        $channel = Channel::factory()->create(); 
        $store = Store::factory()->create();


        $scopes = [ "default", "website", "channel", "store" ];
        $scope = Arr::random($scopes);

        if($scope == "default") $scope_id = 0;
        if($scope == "website") $scope_id = $website->id;
        if($scope == "channel") $scope_id = $channel->id;
        if($scope == "store") $scope_id = $store->id;

        $paths = [ "\Modules\Core\Entities\Currency", "\Modules\User\Entities\Admin", "\Modules\Core\Entities\Website", ];
        $path = Arr::random($paths);

        while(true) {
            $exist_configuration = Configuration::where([
                ['scope', $scope],
                ['scope_id', $scope_id],
                ['path', $path]
            ])->first();
            if (!$exist_configuration) break;
        }

         return [
            'scope' => $scope,
            'scope_id' => $scope_id,
            'path' => $path,
            'value' => $this->faker->name
        ];
    }
}


