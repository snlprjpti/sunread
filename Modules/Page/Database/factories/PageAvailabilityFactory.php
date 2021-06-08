<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageAvailability;

class PageAvailabilityFactory extends Factory
{
    protected $model = PageAvailability::class;

    public function definition(): array
    {
        $model_type = Arr::random(config('model_list.model_types'));
        return [
            "page_id" => Page::latest()->first()->id,
            "model_type" => $model_type,
            "model_id" => rand(1, 10),
            "status" => 1
        ];
    }
}
