<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageConfiguration;

class PageConfigurationFactory extends Factory
{
    protected $model = PageConfiguration::class;

    public function definition(): array
    {
        $scope = Arr::random(config('page.model_config'));
        $scope_id = app($scope["scope"])::factory(1)->create()->first()->id;
        return [
            "page_id" => Page::factory()->create()->id,
            "scope" => $scope,
            "scope_id" => $scope_id,
            "status" => 1,
            "title" => $this->faker->name(),
            "description" => $this->faker->paragraph()
        ];
    }
}

