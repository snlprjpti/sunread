<?php
namespace Modules\Product\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Product\Entities\Product;

class ProductImageFactory extends Factory
{
    protected $model = \Modules\Product\Entities\ProductImage::class;

    public function definition(): array
    {
        return [
            "product_id" => Product::first()->id,
            "position" => $this->faker->randomDigit(),
            "path" => Str::random(20),
            "main_image" => 1,
            "small_image" => 1,
            "thumbnail" => 1
        ];
    }
}

