<?php
namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\Store;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        while(true) {
            $name = $this->faker->name();
            $slug = \Str::slug($name);
            $old_store = Store::where("slug", $slug)->first();
            if (!$old_store) break;
        }

        return [
            "slug" => $slug,
            "name" => $name,
            "currency" => Currency::factory()->create()->code,
            "locale" => $this->faker->locale(),
            "position" => $this->faker->randomDigit()
        ];
    }
}

