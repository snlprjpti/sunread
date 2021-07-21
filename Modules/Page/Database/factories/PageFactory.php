<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class PageFactory extends Factory
{
    protected $model = \Modules\Page\Entities\Page::class;

    public function definition(): array
    {
        for($i = 0; $i < rand(1,2); $i++)
        {
            $data["scope"] = Arr::random([ "website", "store" ]);

            switch ($data["scope"]) {
                case "website":
                    $data["scope_id"] = Website::factory()->create()->id;
                    break;
    
                case "store":
                    $data["scope_id"] = Store::factory()->create()->id;
                    break;
            }
            $scopes[] = $data;
        }      

        return [
            "title" => $this->faker->name(),
            "slug" => $this->faker->unique()->slug(),
            "meta_title" => $this->faker->name(),
            "meta_keywords" => $this->faker->name(),
            "meta_description" => $this->faker->paragraph(),
            "position" => $this->faker->randomDigit(),
            "status" => 1,
            "scopes" => $scopes,
            "attributes" => []
        ];
    }
}
