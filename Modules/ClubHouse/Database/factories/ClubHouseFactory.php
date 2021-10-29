<?php
namespace Modules\ClubHouse\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Website;

class ClubHouseFactory extends Factory
{
    protected $model = \Modules\ClubHouse\Entities\ClubHouse::class;

    public function definition(): array
    {
        $club_house_type = ["resort", "club house"];
        return [
            "position" => $this->faker->randomDigit(),
            "type" => array_rand($club_house_type),
            "website_id" => Website::factory()->create()->id
        ];
    }
}

