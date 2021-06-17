<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\Store;

class StoreFactory extends Factory
{
    protected $model = \Modules\Core\Entities\Store::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $slug = $this->faker->unique()->slug();
        $channel = Channel::factory()->create();

        return [
            "slug" => $slug,
            "name" => $name,
            "currency" => Currency::factory()->create()->code,
            "locale" => $this->faker->locale(),
            "position" => $this->faker->randomDigit(),
            "channel_id" => $channel->id,
        ];
    }
}
