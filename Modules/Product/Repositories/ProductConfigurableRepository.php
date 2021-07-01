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

class ProductConfigurableRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "website_id" => "required|exists:websites,id",
            "sku" => "required|unique:products,sku",
            "attribute_set_id" => "required|exists:attribute_sets,id",
        ];
    }

    public function validateAttributes(object $request): array
    {
        try
        {
            $attributes = Attribute::all();
            $product_attributes = array_map(function ($product_attribute) use ($attributes) {
                if ( !is_array($product_attribute) ) throw ValidationException::withMessages([ "attributes" => "Invalid attributes format." ]);
                $attribute = $attributes->where("id", $product_attribute["attribute_id"])->first() ?? null;
                if ( !$attribute ) throw ValidationException::withMessages([ "attributes" => "Attribute with id {$product_attribute['attribute_id']} does not exist." ]);

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
            $attribute_set = AttributeSet::whereId($attribute_set_id)->with(["attribute_groups"])->firstOrFail();
            $attribute_ids = $attribute_set->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes->pluck('id');
            })->flatten(1)->toArray();

            $attributes = Attribute::whereIn('id', $attribute_ids)->get();
            $check_attribute = $attributes->pluck("id")->toArray();

            array_map(function ($request_attribute) use ($attributes, $check_attribute) {
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

    public function createVariants(object $product, object $request)
    {
       try
       {
            //get previous variants and delete all collection variants
            if ($product->variants->count() > 0) $product->variants()->delete();
            
            //create product-superattribute
            $super_attributes = [];
            foreach ($request->get("super_attributes") as $attributeCode => $attributeOptions) {
                $attribute = Attribute::whereSlug($attributeCode)->first();
                if ($attribute->is_user_defined == 0) continue;
                $super_attributes[$attribute->id] = $attributeOptions;
            }

            //generate multiple product(variant) combination on the basis of color and size for variants
            foreach (array_permutation($super_attributes) as $permutation) {
                $this->addVariant($product, $permutation, $request);
            }
       }
       catch (Exception $exception)
       {
           throw $exception;
       }
       
       return true;
    }

    private function addVariant(object $product, mixed $permutation, object $request): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update-status.before");

        try 
        {
            $data = [
                "parent_id" => $product->id,
                "website_id" => $product->website_id,
                "brand_id" => $product->brand_id,
                "type" => "simple",
                "attribute_set_id" => $product->attribute_set_id,
                "sku" => \Str::slug($product->sku) . "-variant-" . implode("-", $permutation),
            ];
    
            $product_variant = $this->create($data, function ($variant) use ($product, $permutation, $request, &$product_attribute){    
                $product_attribute = [
                    [
                        // Attrubute slug
                        "attribute_id" => 1,
                        "value" => \Str::slug($product->sku)."-variant-".implode("-", $permutation),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ],
                    [
                        // Attrubute name
                        "attribute_id" => 2,
                        "value" => $product->sku."-variant-".implode("-", $permutation),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ]
                ];

                $this->syncAttributes($product_attribute, $variant);
                
                $variant->categories()->sync($request->get("categories"));
                $variant->channels()->sync($request->get("channels"));
            });
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
        
        Event::dispatch("{$this->model_key}.attibutes.sync.after", $product_attribute);
        DB::commit();

        return $product_variant;
    }

}
