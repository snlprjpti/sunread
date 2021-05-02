<?php
namespace Modules\Core\Database\factories;

use Modules\Core\Entities\Store;
use Modules\Core\Entities\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Channel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $store = Store::factory()->create();
        $website=Website::factory()->create();

        while(true) {
            $code = \Str::random(16);
            $old_channel = Channel::where("code", $code)->first();
            if (!$old_channel) break;
        }

        return [
            "code" => $code,
            "hostname" => $code,
            "name" => $this->faker->company(),
            "description" => $this->faker->paragraph(),
            "location" => $this->faker->address(),
            "timezone" => $this->faker->timezone(),
            "theme" => "default",
            "default_store_id" => $store->id,
            "default_currency" => $store->currency,
            "website_id" => $website->id
        ];
    }
}

