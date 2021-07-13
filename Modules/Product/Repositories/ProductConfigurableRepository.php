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
            "scope" => "sometimes|in:website,channel,store"
        ];
        $this->attribute = $attribute;
        $this->product_repository = $product_repository;
        $this->non_required_attributes = [ "price", "cost", "quantity_and_stock_status" ];
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

            $productAttributes = collect($request_attributes)->reject(function ($item) {
                return (($item["attribute_slug"] == "name") || ($item["attribute_slug"] == "sku"));
            })->toArray();

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
                    "attribute_slug" => Attribute::find($key)->slug,
                    "value" => $option,
                    "value_type" => config("attribute_types")[(Attribute::findOrFail($key)->type) ?? "string"]
                ];
            })->toArray();

            $product_variant = $this->create($data, function ($variant) use ($product, $permutation_modify, $request, &$product_attributes, $productAttributes, $scope, $variant_options){    
                
                $product_attributes = array_merge([
                    [
                        // Attrubute name
                        "attribute_slug" => "name",
                        "value" => $product->sku."-variant-".implode("-", $permutation_modify),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ],
                    [
                        //Attribute slug
                        "attribute_slug" => "sku",
                        "value" => Str::slug($product->sku)."-variant-".implode("-", $permutation_modify),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ]
                ], $productAttributes, $variant_options);

                $this->product_repository->syncAttributes($product_attributes, $variant, $scope, $request, "store");
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
}
