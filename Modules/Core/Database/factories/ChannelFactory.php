<?php
namespace Modules\Core\Database\factories;

use Modules\Core\Entities\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Website;

class ChannelFactory extends Factory
{
    protected $model = \Modules\Core\Entities\Channel::class;

    public function definition(): array
    {
        $website = Website::factory()->create();
        $category = Category::factory()->create();
        $code = $this->faker->unique()->slug();

        return [
            "code" => $code,
            "hostname" => $code,
            "name" => $this->faker->company(),
            "description" => $this->faker->paragraph(),
            "location" => $this->faker->address(),
            "timezone" => $this->faker->timezone(),
            "theme" => "default",
            "default_store_id" => null,
            "default_currency" => null,
            "website_id" => $website->id,
            "default_category_id" => $category->id
        ];
    }
}
