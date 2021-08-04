<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Website;

class PageFactory extends Factory
{
    protected $model = \Modules\Page\Entities\Page::class;

    public function definition(): array
    {      
        return [
            "title" => $this->faker->name(),
            "slug" => $this->faker->unique()->slug(),
            "meta_title" => $this->faker->name(),
            "meta_keywords" => $this->faker->name(),
            "meta_description" => $this->faker->paragraph(),
            "position" => $this->faker->randomDigit(),
            "website_id" => Website::factory()->create()->id,
            "status" => 1
        ];
    }
}
