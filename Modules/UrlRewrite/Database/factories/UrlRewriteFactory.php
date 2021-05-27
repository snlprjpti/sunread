<?php
namespace Modules\UrlRewrite\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;

class UrlRewriteFactory extends Factory
{
    protected $model = \Modules\UrlRewrite\Entities\UrlRewrite::class;

    public function definition(): array
    {
        $types = [ "Product", "Category" ];
        $type = Arr::random($types);
        $request_path = "";

        switch ($type) {
            case "Product":
                $product_attribute = ProductAttribute::withoutEvents(function (){
                    return ProductAttribute::factory()->create();
                });
                
                $parameter_id = $product_attribute->product_id;
                if($product_attribute->store_id != null)
                {
                    $store_id = $product_attribute->store_id;
                    $request_path = "{$product_attribute->store->slug}/";
                }

                $request_path .= $this->faker->unique()->slug();
                break;

            case "Category":
                $category = Category::withoutEvents(function (){
                    return Category::factory()->create();
                });

                $parameter_id = $category->id;
                $request_path .= $category->slug;
                break;
        }

        return [
            "type" => $type,
            "parameter_id" => $parameter_id,
            "request_path" => $request_path,
            'store_id' => isset($store_id) ? $store_id : null,
            "created_at" => now(),
            "updated_at" => now()
        ];
    }
}

