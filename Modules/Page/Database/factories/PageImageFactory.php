<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageImage;

class PageImageFactory extends Factory
{
    protected $model = PageImage::class;

    public function definition(): array
    {
        return [
            "page_id" => Page::factory()->create()->id,
            "path" => Str::random(20),
            "is_banner" => 0,
            "is_slider" => 0,
        ];
    }
}

