<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Validator;
use Modules\Attribute\Entities\Attribute;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Product\Entities\ProductAttribute;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "parent_id" => "sometimes|nullable|exists:products,id",
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "attribute_group_id" => "required|exists:attribute_groups,id",
            "sku" => "required|unique:products,sku",
            "type" => "required|in:simple,configurable",
            "status" => "sometimes|boolean",
            "attributes" => "required|array"
        ];
    }

    public function validateAttributes(object $request): array
    {
        $attributes = Attribute::all();
        $product_attributes = array_map(function($product_attribute) use ($attributes) {
            $attribute = $attributes->where("id", $product_attribute["attribute_id"])->first() ?? null;
            if ( !$attribute ) throw ValidationException::withMessages([ "attributes" => "Attribute with id {$product_attribute["attribute_id"]} does not exist." ]);

            $validator = Validator::make($product_attribute, [
                "store_id" => "sometimes|nullable|exists:stores,id",
                "channel_id" => "sometimes|nullable|exists:channels,id",
                "value" => $attribute->validation
            ]);
            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());

            $attribute_type = config("attribute_types")[$attribute->type ?? "string"];
            return array_merge($product_attribute, ["value_type" => $attribute_type], $validator->valid());
        }, $request->get("attributes"));
        
        
        return $product_attributes;
    }

    public function syncAttributes(array $data, object $product): bool
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.attibutes.sync.before");

        try
        {
            foreach($data as $attribute) {
                $match = ["product_id" => $product->id];
                foreach (["attribute_id", "store_id", "channel_id"] as $field) {
                    if (isset($attribute[$field])) $match[$field] = $attribute[$field];
                }

                $product_attribute = ProductAttribute::updateOrCreate($match, $attribute);

                if ( $product_attribute->value_id != null ) {
                    $product_attribute->value()->update(["value" => $attribute["value"]]);
                    continue;
                }

                ProductAttribute::where($match)->update(["value_id" => $attribute["value_type"]::create(["value" => $attribute["value"]])->id]);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.attibutes.sync.after", $product_attribute);
        DB::commit();

        return true;
    }
}
