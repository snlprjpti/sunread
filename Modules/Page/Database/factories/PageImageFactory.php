<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Page\Entities\Page;

class PageImageFactory extends Factory
{
    protected $model = \Modules\Page\Entities\PageImage::class;

    public function definition(): array
    {
        return [
            "product_id" => Page::latest()->first()->id,
            "path" => Str::random(20),
        ];
    }
}

