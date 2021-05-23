<?php
namespace Modules\Product\Database\factories;

use Modules\Core\Entities\Store;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Entities\Product;

class ProductAttributeFactory extends Factory
{
    protected $model = \Modules\Product\Entities\ProductAttribute::class;

    public function definition(): array
    {
        $attribute_types = config("attribute_types");
        $attribute_type = array_rand($attribute_types);
        $attribute_model = $attribute_types[$attribute_type];

        return [
            "attribute_id" => Attribute::factory()->create(["type" => $attribute_type])->id,
            "channel_id" => null,
            "store_id" => Store::factory()->create()->id,
            "value_type" => $attribute_model,
            "value_id" => $attribute_model::factory()->create()->id
        ];
    }
}
