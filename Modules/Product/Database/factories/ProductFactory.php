<?php
namespace Modules\Product\Database\factories;

use Illuminate\Support\Arr;
use Modules\Brand\Entities\Brand;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\AttributeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \Modules\Product\Entities\Product::class;

    public function definition(): array
    {
        return [
            "parent_id" => null,
            "brand_id" => Brand::factory()->create()->id,
            "attribute_group_id" => AttributeGroup::factory()->create()->id,

            "sku" => $this->faker->slug(),
            "type" => "simple",
            "status" => 1
        ];
    }

    public function configurable(): Self
    {
        return $this->state(function(array $attributes) {
            return [
                "parent_id" => null,
                "type" => "configurable"
            ];
        });
    }
}

