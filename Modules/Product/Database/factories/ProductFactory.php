<?php
namespace Modules\Product\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attribute\Entities\AttributeGroup;

class ProductFactory extends Factory
{
    protected $model = \Modules\Product\Entities\Product::class;

    public function definition(): array
    {
        return [
            "parent_id" => null,
            "brand_id" => null, // TODO::Brand::factory()->create()->id
            "attribute_group_id" => AttributeGroup::factory()->create()->id,

            "sku" => $this->faker->slug(),
            "type" => "simple",
            "status" => 1
        ];
    }
}

