<?php
namespace Modules\UrlRewrite\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\ProductAttribute;

class UrlRewriteFactory extends Factory
{
    protected $model = \Modules\UrlRewrite\Entities\UrlRewrite::class;

    public function definition(): array
    {
        $types = [ "product", "category" ];
        $type = Arr::random($types);
        $request_path = "";

        switch ($type) {
            case "product":
                $product_attribute = ProductAttribute::factory()->create();
                $parameter_id = $product_attribute->product_id;
                if($product_attribute->store_id != null)
                {
                    $store_id = $product_attribute->store_id;
                    $request_path = "{$product_attribute->store->slug}/";
                }
                $request_path .= $product_attribute->value->value;
                break;

            case "category":
                $category = Category::factory()->create();
                $parameter_id = $category->id;
                $request_path .= $category->slug;
                break;
        }

        return [
            "type" => $type,
            "parameter_id" => $parameter_id,
            "request_path" => $request_path,
            'store_id' => $store_id
        ];
    }
}

