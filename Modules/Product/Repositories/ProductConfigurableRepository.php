<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Validator;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Support\Str;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Attribute\Entities\AttributeSet;

class ProductConfigurableRepository extends BaseRepository
{
    protected $attribute, $product_repository, $non_required_attributes;

    public function __construct(Product $product, Attribute $attribute, ProductRepository $product_repository)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "super_attributes" => "required|array",
            "attributes" => "required|array",
            "scope" => "sometimes|in:global,website,channel,store"
        ];
        $this->attribute = $attribute;
        $this->product_repository = $product_repository;
        $this->non_required_attributes = [ "price", "cost", "quantity_and_stock_status" ];
    }

    public function validateAttributes(object $product, object $request, array $scope): array
    {
        try
        {
            $attribute_set = AttributeSet::whereId($product->attribute_set_id)->firstOrFail();
            
            $attributes = $attribute_set->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes;
            })->flatten(1);

            $request_attribute_ids = array_map( function ($request_attribute) {
                return $request_attribute["attribute_id"];
            }, $request->get("attributes"));

            $request_attribute_collection = collect($request->get("attributes"));

            $all_product_attributes = [];

            $super_attributes = Arr::pluck($request->super_attributes, 'attribute_id');
            
            foreach ( $attributes as $attribute)
            {
                $product_attribute = [];
                if($this->product_repository->scopeFilter($scope["scope"], $attribute->scope)) continue; 

                if($request->super_attributes && (in_array($attribute->id, $super_attributes))) continue;

                $single_attribute_collection = $request_attribute_collection->where('attribute_id', $attribute->id);
                $default_value_exist = $single_attribute_collection->pluck("use_default_value")->first();

                $product_attribute["attribute_id"] = $attribute->id;
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

                $all_product_attributes[] = array_merge($product_attribute, ["value_type" => $attribute_type], $validator->valid()); 
            }
            $all_product_attributes = $this->product_repository->unsetter($all_product_attributes, $request_attribute_ids);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $all_product_attributes;
    }

    public function syncAttributes(array $data, object $product, array $scope): bool
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.attibutes.sync.before");

        try
        {
            $non_required = Attribute::whereIn("slug", $this->non_required_attributes)->get()->pluck('id')->toArray();

            foreach($data as $attribute) { 

                if(in_array($attribute['attribute_id'], $non_required)) continue;

                $match = [
                    "product_id" => $product->id,
                    "scope" => $scope["scope"],
                    "scope_id" => $scope["scope_id"],
                    "attribute_id" => $attribute['attribute_id']
                ];

                if(isset($attribute["use_default_value"]))
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

                $product_attribute->update(["value_id" => $attribute["value_type"]::create(["value" => $attribute["value"]])->id]);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        if($product_attribute) Event::dispatch("{$this->model_key}.attibutes.sync.after", $product_attribute);
        DB::commit();

        return true;
    }

    public function createVariants(object $product, object $request, array $scope, array $request_attributes)
    {
       try
       {
            //get previous variants and delete all collection variants
            if ($product->variants->count() > 0) $product->variants()->delete();
            
            //create product-superattribute
            $super_attributes = [];
            
            foreach ($request->get("super_attributes") as $super_attribute) {
                $attribute = Attribute::findorFail($super_attribute['attribute_id']);
                if ($attribute->is_user_defined == 1 && $attribute->type != "select") continue;
                $super_attributes[$attribute->id] = $super_attribute["value"];
            }

            $productAttributes = collect($request_attributes)->where('attribute_id', '!=', (Attribute::whereSlug("name")->first()->id))->toArray();

            //generate multiple product(variant) combination on the basis of color and size for variants
            foreach (array_permutation($super_attributes) as $permutation) {
                $this->addVariant($product, $permutation, $request, $productAttributes, $scope);
            }
       }
       catch ( Exception $exception )
       {
           throw $exception;
       }
       
       return true;
    }

    private function addVariant(object $product, mixed $permutation, object $request, array $productAttributes, array $scope): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update-status.before");

        try 
        {
            $permutation_modify = AttributeOption::whereIn('id', $permutation)->get()->pluck('name')->toArray();
            $data = [
                "parent_id" => $product->id,
                "website_id" => $product->website_id,
                "brand_id" => $product->brand_id,
                "type" => "simple",
                "attribute_set_id" => $product->attribute_set_id
            ];
            $product_attributes = [];


            $variant_options = collect($permutation)->map(function ($option, $key) {
                return [
                    "attribute_id" => $key,
                    "value" => $option,
                    "value_type" => config("attribute_types")[(Attribute::findOrFail($key)->type) ?? "string"]
                ];
            })->toArray();

            $product_variant = $this->create($data, function ($variant) use ($product, $permutation_modify, $request, &$product_attributes, $productAttributes, $scope, $variant_options){    
                
                $product_attributes = array_merge([
                    [
                        // Attrubute slug
                        "attribute_id" => Attribute::whereSlug("name")->first()->id,
                        "value" => $product->sku."-variant-".implode("-", $permutation_modify),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ]
                ], $productAttributes, $variant_options);
                $this->product_repository->syncAttributes($product_attributes, $variant, $scope);

                $request_data = $request->toArray();
                $request_data["attributes"] = array_merge([
                    [
                        "attribute_id" => Attribute::whereSlug("sku")->first()->id,
                        "value" => Str::slug($product->sku)."-variant-".implode("-", $permutation_modify),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ]
                ], $product_attributes);

                $this->product_repository->attributeMapperSync($variant, new Request($request_data));
                $variant->channels()->sync($request->get("channels"));
            });
        }
        catch ( Exception $exception )
        {
            DB::rollBack();
            throw $exception;
        }
        
        Event::dispatch("{$this->model_key}.attibutes.sync.after", $product_variant);
        DB::commit();

        return $product_variant;
    }

    public function attributeMapperSync(object $product, object $request, string $method = "store"): bool
    {
        try
        {
            $this->product_repository->sku($product, $request, $method);
            $this->product_repository->status($product, $request);
            $this->product_repository->categories($product, $request);
            $this->product_repository->images($product, $request);
        }
        catch ( Exception $exception )
        {
            throw $exception;  
        }

        return true;
    }
}
