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
        $config_types = [ "product", "category" ];
        $config_type = Arr::random($config_types);
        $config_base_name = "url-rewrite.types.{$config_type}";
        $route = config("{$config_base_name}.route");
        $request_path = "";

        switch ($config_type) {
            case "product":
                $product_attribute = ProductAttribute::factory()->create();
                $type_attributes["parameter"]["product"] = $product_attribute->product_id;
                if($product_attribute->store_id != null)
                {
                    $type_attributes["extra_fields"]["store_id"] = $product_attribute->store_id;
                    $request_path = "{$product_attribute->store->slug}/";
                }
                $request_path .= $product_attribute->value->value;
                break;

            case "category":
                $category = Category::factory()->create();
                $type_attributes = [
                    "parameter" => [
                        "category" => $category->id
                    ]
                ];
                $request_path .= $category->slug;
                break;
        }

        return [
            "type" => $route,
            "type_attributes" => $type_attributes,
            "request_path" => $request_path,
            "target_path" => route($route, $type_attributes['parameter'], false),
            "redirect_type" => 0
        ];
    }
}

