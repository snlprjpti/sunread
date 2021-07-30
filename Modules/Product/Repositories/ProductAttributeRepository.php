<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Product\Entities\ProductAttribute;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Modules\Attribute\Entities\AttributeSet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Tax\Entities\CustomerTaxGroup;

class ProductAttributeRepository extends ProductRepository
{
    protected $option_fields, $attributeMapperSlug, $functionMapper, $product_repository, $non_option_slug, $non_required_attributes;

    public function __construct(ProductAttribute $productAttribute, ProductRepository $product_repository)
    {
        $this->model = $productAttribute;
        $this->model_key = "catalog.products.attibutes";
        $this->product_repository = $product_repository;

        $this->option_fields = [ "select", "multiselect", "checkbox", "multiimages" ];

        $this->attributeMapperSlug = [ "quantity_and_stock_status", "sku", "status", "category_ids", "base_image", "small_image", "thumbnail_image" ];
        $this->functionMapper = [
            "sku" => "sku",
            "status" => "status",
            "quantity_and_stock_status" => "catalogInventory",
            "category_ids" => "categories",
            "base_image" => "base_image", 
            "small_image" => "small_image",
            "thumbnail_image" => "thumbnail_image"
        ];
        $this->non_required_attributes = [ "price", "cost", "quantity_and_stock_status" ];
        $this->non_option_slug = [ "tax_class_id", "category_ids" ];
    }

    public function attributeSetCache(): object
    {
        try
        {
            if (!Cache::has("attributes_attribute_set"))
            {
                Cache::remember("attributes_attribute_set", Carbon::now()->addDays(2) ,function () {
                    return AttributeSet::with([ "attribute_groups.attributes" ])->get();
                });
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return Cache::get("attributes_attribute_set");
    }

    public function validateAttributes(object $product, object $request, array $scope, ?string $product_type = null): array
    {
        try
        {

            $attribute_set = AttributeSet::where("id", $product->attribute_set_id)->first();
            //$attribute_set = $this->attributeSetCache()->where("id", $product->attribute_set_id)->first();

            // get all the attributes of following attribute set
            $attributes = $attribute_set->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes;
            })->flatten(1);

            // get all request attribute id
            $request_attribute_ids = array_map( function ($request_attribute) {

                if(!isset($request_attribute["attribute_id"])) throw ValidationException::withMessages(["attribute_id" => "Invalid attribute format."]);
                return $request_attribute["attribute_id"];
                
            }, $request->get("attributes"));

            $request_attribute_collection = collect($request["attributes"]);

            $all_product_attributes = [];

            if($product_type) $super_attributes = Arr::pluck($request->super_attributes, 'attribute_id');
            
            foreach ( $attributes as $attribute )
            {
                $product_attribute = [];

                //Scope Filter
                if($this->product_repository->scopeFilter($scope["scope"], $attribute->scope)) continue; 

                //Super attribute filter in case of configurable products
                if(isset($super_attributes) && (in_array($attribute->id, $super_attributes))) continue;

                $single_attribute_collection = $request_attribute_collection->where('attribute_id', $attribute->id);
                $default_value_exist = $single_attribute_collection->pluck("use_default_value")->first();

                $product_attribute["attribute_slug"] = $attribute->slug;
                if($default_value_exist == 1)
                {
                    $product_attribute["use_default_value"] = 1;
                    $all_product_attributes[] = $product_attribute;
                    continue;
                }
                $product_attribute["value"] = in_array($attribute->id, $request_attribute_ids) ? $single_attribute_collection->pluck("value")->first() : null;
                $attribute_type = config("attribute_types")[$attribute->type ?? "string"];

                $validator = Validator::make($product_attribute, [
                    "value" => $attribute->type_validation
                ]);
                
                if ( $validator->fails() ) throw ValidationException::withMessages([$attribute->name => $validator->errors()->toArray()]);

                if(isset($product_attribute["value"]) && in_array($attribute->type, $this->option_fields)) $this->optionValidation($attribute, $product_attribute["value"]);
                
                if($attribute->slug == "quantity_and_stock_status") $product_attribute["catalog_inventory"] = $single_attribute_collection->pluck("catalog_inventory")->first();

                $all_product_attributes[] = array_merge($product_attribute, ["value_type" => $attribute_type], $validator->valid()); 
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return collect($all_product_attributes)->where("value", "!=", null)->toArray();
    }

    public function optionValidation(object $attribute, mixed $values): void
    {
        if(in_array($attribute->type, ["checkbox", "multiselect"])) $this->multipleOptionValidation($attribute, $values);
        if(in_array($attribute->type, ["select"])) $this->singleOptionValidation($attribute, $values);
        if(in_array($attribute->type, ["multiimages"])) $this->multipleImageValidation($attribute, $values);
    }

    public function singleOptionValidation(object $attribute, mixed $values): void
    {
        if(in_array($attribute->slug, $this->non_option_slug))
        {
            if($attribute->slug == "tax_class_id") $attribute_options = CustomerTaxGroup::pluck("id")->toArray();
        }
        else $attribute_options = AttributeOption::whereAttributeId($attribute->id)->pluck("id")->toArray();

        if(isset($attribute_options) && !in_array($values, $attribute_options)) throw ValidationException::withMessages([$attribute->name => "Invalid Attribute option"]);

    }

    public function multipleOptionValidation(object $attribute, mixed $values): void
    {
        foreach($values as $value)
        {
            $this->singleOptionValidation($attribute, $value);
        }
    }

    public function multipleImageValidation(object $attribute, mixed $values): void
    {
        try
        {
            foreach($values as $value)
            {
                $images["value"] = $value;
                $validator = Validator::make($images, [
                    "value" => "mimes:bmp,jpeg,jpg,png"
                ]);
                if ( $validator->fails() ) throw ValidationException::withMessages([$attribute->name => $validator->errors()->toArray()]);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function syncAttributes(array $data, object $product, array $scope, object $request, string $method = "store", ?string $product_type = null): bool
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");

        try
        {
            foreach($data as $attribute) { 

                //removed some attributes in case of configurable products
                if($product_type && in_array($attribute['attribute_slug'], $this->non_required_attributes)) continue;

                if( in_array($attribute["attribute_slug"], $this->attributeMapperSlug) )
                {
                    // store mapped attributes on respective function. ( sku, categories.)
                    $function_name = $this->functionMapper[$attribute["attribute_slug"]];
                    $this->product_repository->$function_name($product, $request, $method, $attribute);
                    continue;
                }
                
                $match = [
                    "product_id" => $product->id,
                    "scope" => $scope["scope"],
                    "scope_id" => $scope["scope_id"],
                    "attribute_id" => Attribute::whereSlug($attribute['attribute_slug'])->first()->id
                ];

                if(isset($attribute["use_default_value"]) && $attribute["use_default_value"] == 1)
                {
                    $product_attribute = ProductAttribute::where($match)->first();
                    if($product_attribute) $product_attribute->delete();
                    continue;
                }

                $product_attribute = ProductAttribute::updateOrCreate($match, $attribute);

                if ( $product_attribute->value_id != null ) {
                    $product_attribute->value()->each(function($attribute_value) use($attribute){
                        $attribute_value->update(["value" => $attribute["value"]]);
                    });
                    continue;
                }
                // store attribute value on attribute type table
                $product_attribute->update(["value_id" => $attribute["value_type"]::create(["value" => $attribute["value"]])->id]);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        if(isset($product_attribute)) Event::dispatch("{$this->model_key}.sync.after", $product_attribute);
        DB::commit();

        return true;
    }
}
