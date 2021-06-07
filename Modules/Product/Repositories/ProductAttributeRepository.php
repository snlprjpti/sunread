<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Product\Entities\ProductAttribute;

class ProductAttributeRepository extends ProductRepository
{
    public function __construct(ProductAttribute $productAttribute)
    {
        $this->model = $productAttribute;
        $this->model_key = "catalog.products.attibutes";
        $this->rules = [
             "attributes" => "required|array",
             "product_id" => "required|exists:products,id"
         ];
    }

    public function createOrUpdate(array $attributes, array $data): array
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");

        try
        {
            $items = [];
            $match = [
                "product_id" => $data["product_id"],
                "store_id" => (isset($data["scope"]) && $data["scope"] == "store") ? $data["scope_id"] : null,
                "channel_id" => (isset($data["scope"]) && $data["scope"] == "channel") ? $data["scope_id"] : null
            ];

            foreach($attributes as $attribute) {
                $match["attribute_id"] = $attribute["attribute_id"];
                $match["value_type"] = $attribute["value_type"];

                $product_attribute = ProductAttribute::where($match)->first();
                if ($product_attribute) {
                    $product_attribute->value()->each(function($attribute_value) use($attribute){
                        $attribute_value->update(["value" => $attribute["value"]]);
                    });
                    $items[] = $product_attribute;
                    continue;
                }

                $product_attribute_value = $attribute["value_type"]::create(["value" => $attribute["value"]]);
                $match["value_id"] = $product_attribute_value->id;
                $items[] = ProductAttribute::create($match);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $product_attribute);
        DB::commit();
        return $items;
    }
}
