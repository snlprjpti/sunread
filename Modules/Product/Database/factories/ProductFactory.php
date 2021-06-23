<?php
namespace Modules\Product\Database\factories;

use Illuminate\Support\Arr;
use Modules\Brand\Entities\Brand;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Website;

class ProductFactory extends Factory
{
    protected $model = \Modules\Product\Entities\Product::class;

    public function definition(): array
    {
        $attribute_set_id = AttributeSet::factory()->create()->id;
        $attribute_group = AttributeGroup::factory(1)
        ->create(["attribute_set_id" => $attribute_set_id])
        ->each(function ($attr_group){
            $attr_group->attributes()->attach(Attribute::factory(1)->create());
        })->first();

        return [
            "parent_id" => null,
            "brand_id" => Brand::factory()->create()->id,
            "website_id" => Website::factory()->create()->id,
            "attribute_set_id" => $attribute_set_id,
            "sku" => $this->faker->unique()->slug(),
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

