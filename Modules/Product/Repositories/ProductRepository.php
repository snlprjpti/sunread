<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Validator;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeSet;
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
            "attribute_set_id" => "required|exists:attribute_sets,id",
            "website_id" => "required|exists:websites,id",
            "sku" => "required|unique:products,sku",
            "status" => "sometimes|boolean",
            "attributes" => "required|array",
            "categories" => "required|array",
            "categories.*" => "required|exists:categories,id"
        ];
    }

    public function validateAttributes(object $request): array
    {
        try
        {
            $attributes = Attribute::all();
            $product_attributes = array_map(function($product_attribute) use ($attributes) {
                if ( !is_array($product_attribute) ) throw ValidationException::withMessages([ "attributes" => "Invalid attributes format." ]);
                $attribute = $attributes->where("id", $product_attribute["attribute_id"])->first() ?? null;
                if ( !$attribute ) throw ValidationException::withMessages([ "attributes" => "Attribute with id {$product_attribute["attribute_id"]} does not exist." ]);

                $validator = Validator::make($product_attribute, [
                    "store_id" => "sometimes|nullable|exists:stores,id",
                    "channel_id" => "sometimes|nullable|exists:channels,id",
                    "value" => $attribute->type_validation
                ]);
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());

                $attribute_type = config("attribute_types")[$attribute->type ?? "string"];
                return array_merge($product_attribute, ["value_type" => $attribute_type], $validator->valid());
            }, $request->get("attributes"));
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
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
                    $product_attribute->value()->each(function($attribute_value) use($attribute){
                        $attribute_value->update(["value" => $attribute["value"]]);
                    });
                    continue;
                }

                $product_attribute->update(["value_id" => $attribute["value_type"]::create(["value" => $attribute["value"]])->id]);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.attibutes.sync.after", $product_attribute);
        DB::commit();

        return true;
    }

    public function checkAttribute(int $attribute_set_id, object $request): bool
    {
        try
        {
            $attribute_set = AttributeSet::whereId($attribute_set_id)->firstOrFail();
            $attribute_ids = $attribute_set->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes->pluck('id');
            })->flatten(1)->toArray();
            $attributes = Attribute::whereIn('id', $attribute_ids)->get();

            $check_attribute = $attributes->pluck("id")->toArray();
            array_map(function($request_attribute) use ($attributes) {
                // check required attribute has value.
                $required_attribute = $attributes->where("is_required", 1);
                if ($required_attribute->count() > 0 && $request_attribute["value"] == "") throw ValidationException::withMessages([ "attributes" => "The Attribute id {$request_attribute['attribute_id']} value is required."]);
                // check attribute exists on attribute set
                $check_attribute = $attributes->pluck("id")->toArray();
                if (!in_array($request_attribute["attribute_id"], $check_attribute)) throw ValidationException::withMessages([ "attributes" => "Attribute id {$request_attribute['attribute_id']} dosen't exists on current attribute set"]);
                return;
            }, $request->get("attributes"));
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
        return true;
    }

    public function extra_rules(object $request): array
    {
        if ($request->quantity_and_stock_status)
        {
            $rule = [
                "quantity_and_stock_status.manage_stock" => "required|boolean",
                "quantity_and_stock_status.is_in_stock" => "required|boolean",
                "quantity_and_stock_status.quantity" => "required|decimal",
                "quantity_and_stock_status.use_config_manage_stock" => "required|boolean"
            ];
        }
        return $rule ?? [];
    }

    public function catalogInventory(object $product, object $request)
    {
        try
        {
            dd($product);
            
        }
        catch ( Exception $exception )
        {

        }

        return true;
    }
}
