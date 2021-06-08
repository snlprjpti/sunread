<?php
namespace Modules\Product\Database\factories;

use Modules\Core\Entities\Store;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Channel;
use Modules\Product\Entities\Product;

class ProductAttributeFactory extends Factory
{
    protected $model = \Modules\Product\Entities\ProductAttribute::class;

    public function definition(): array
    {
        $attribute_types = config("attribute_types");
        unset($attribute_types["image"], $attribute_types["file"]);
        $attribute_type = array_rand($attribute_types);
        $attribute_model = $attribute_types[$attribute_type];

        $input["scope"] = Arr::random([ "channel", "store" ]);
        switch ($input["scope"]) {
            case "channel":
                $input["scope_id"] = Channel::factory()->create()->id;
                break;

            case "store":
                $input["scope_id"] = Store::factory()->create()->id;
                break;
        }

        $item["product_id"] = Product::factory()->create()->id;

        for($i=0; $i < rand(1,15); $i++)
        {
            $data["attribute_id"] = Attribute::factory()->create(["type" => $attribute_type])->id;
            $data["value"] = $attribute_model::factory()->make()->value;
            $item["attributes"][] = $data;
        }

        $itemOption = [];
        array_push($itemOption, $item);
        array_push($itemOption, array_merge($item, $input));
        
        return $itemOption[rand(0,1)];
        
    }
}
