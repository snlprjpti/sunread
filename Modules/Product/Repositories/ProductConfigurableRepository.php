<?php

namespace Modules\Product\Repositories;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Support\Str;
use Modules\Core\Repositories\BaseRepository;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Product\Entities\AttributeConfigurableProduct;

class ProductConfigurableRepository extends BaseRepository
{
    protected $attribute, $product_repository, $non_required_attributes, $product_attribute_repository, $configurable_attributes;

    public function __construct(Product $product, Attribute $attribute, ProductRepository $product_repository, ProductAttributeRepository $productAttributeRepository)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "super_attributes" => "required|array",
            "attributes" => "required|array",
            "scope" => "sometimes|in:website,channel,store",
            "website_id" => "required|exists:websites,id"
        ];
        $this->attribute = $attribute;
        $this->product_repository = $product_repository;
        $this->product_attribute_repository = $productAttributeRepository;
        $this->non_required_attributes = [ "price", "cost", "quantity_and_stock_status" ];
        $this->asd = $this->attributeCache();
    }

    public function attributeCache(): Collection
    {
        try
        {
            if ( !Cache::has("attributes"))
            {
                Cache::remember("attributes", Carbon::now()->addDays(2) ,function () {
                    return Attribute::with([ "attribute_options" ])->get();
                });
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return Cache::get("attributes");
    }

    public function attributeOptionsCache(): Collection
    {
        try
        {
            if(!Cache::has("attribute_options"))
            {
                Cache::remember("attribute_options", Carbon::now()->addDays(2) ,function () {
                    return AttributeOption::with([ "attribute" ])->get();
                });
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return Cache::get("attribute_options");
    }

    public function createVariants(object $product, object $request, array $scope, array $request_attributes, ?string $method = null)
    {
       try
       {    
            //create product-superattribute
            $super_attributes = [];
            $this->configurable_attributes = [];
            
            foreach ($request->get("super_attributes") as $super_attribute) {
                $attribute = $this->attributeCache()->find($super_attribute['attribute_id']);
                if ($attribute->is_user_defined == 0 && $attribute->type != "select") continue;
                foreach($super_attribute["value"] as $super_val) $this->product_attribute_repository->singleOptionValidation($attribute, $super_val);
                $super_attributes[$attribute->id] = $super_attribute["value"];
            }

            $productAttributes = collect($request_attributes)->reject(function ($item) {
                return (($item["attribute_slug"] == "name") || ($item["attribute_slug"] == "sku"));
            })->toArray();

            //generate multiple product(variant) combination on the basis of color, size (super_attributes/user defined attributes) for variants
            foreach (array_permutation($super_attributes) as $permutation) {
                $product_variant_data[] = $this->addVariant($product, $permutation, $request, $productAttributes, $scope, $method);
            }
            
            $product->variants()->whereNotIn('id', array_filter(Arr::pluck($product_variant_data, 'id')))->get()->map(function($single_product) {
                $this->product_repository->delete($single_product->id);
            });
       }
       catch ( Exception $exception )
       {
           throw $exception;
       }
       
       return true;
    }

    private function addVariant(object $product, mixed $permutation, object $request, array $productAttributes, array $scope, ?string $method): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update-status.before");

        try 
        {
            $permutation_modify = $this->attributeOptionsCache()->whereIn('id', $permutation)->pluck('name')->toArray();
            $data = [
                "parent_id" => $product->id,
                "website_id" => $product->website_id,
                "brand_id" => $product->brand_id,
                "type" => "simple",
                "attribute_set_id" => $product->attribute_set_id
            ];
            $product_attributes = [];
            $this->configurable_attributes = [];

            $variant_options = collect($permutation)->map(function ($option, $key) {
                $this->configurable_attributes[] = [
                    "attribute_id" => $key,
                    "attribute_option_id" => $option
                ];
                return [
                    "attribute_slug" => $this->attributeCache()->find($key)->slug,
                    "value" => $option,
                    "value_type" => config("attribute_types")[($this->attributeCache()->find($key)->type) ?? "string"]
                ];
            })->toArray();

            if($method && $method == "update") $child_variant = $this->checkVariant($product);

            // create variant simple product
            if(isset($child_variant))
            {
                $product_variant = $this->update($data, $child_variant->id, function ($variant) use ($product, $permutation_modify, $request, &$product_attributes, $productAttributes, $scope, $variant_options) {
                    $this->syncConfigurableAttributes($product, $permutation_modify, $request, $product_attributes, $productAttributes, $scope, $variant_options, $variant);
                });
            }
            else
            {
                $product_variant = $this->create($data, function ($variant) use ($product, $permutation_modify, $request, &$product_attributes, $productAttributes, $scope, $variant_options) {
                    $this->syncConfigurableAttributes($product, $permutation_modify, $request, $product_attributes, $productAttributes, $scope, $variant_options, $variant);
                });
            }
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

    private function checkVariant(object $product): ?object
    {
        try 
        {
            foreach($product->variants as $child_variant)
            {
                    $exist_variant = $child_variant->attribute_configurable_products()
                    ->whereIn("attribute_id", collect($this->configurable_attributes)->pluck("attribute_id")->toArray())
                    ->whereIn("attribute_option_id",collect($this->configurable_attributes)->pluck("attribute_option_id")->toArray())
                    ->get();

                    if($exist_variant && count($exist_variant) == count($this->configurable_attributes)) 
                    {
                        $data = $child_variant;
                        break;
                    }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $data ?? null;
    }

    private function syncConfigurableAttributes(object $product, array $permutation_modify, object $request, array &$product_attributes, array $productAttributes, array $scope, array $variant_options, object $variant): void
    {
        try 
        {
            $product_attributes = array_merge([
                [
                    // Attrubute name
                    "attribute_slug" => "name",
                    "value" => $product->sku."_".implode("_", $permutation_modify),
                    "value_type" => "Modules\Product\Entities\ProductAttributeString"
                ],
                [
                    //Attribute slug
                    "attribute_slug" => "sku",
                    "value" => Str::slug($product->sku)."_".implode("_", $permutation_modify),
                    "value_type" => "Modules\Product\Entities\ProductAttributeString"
                ]
            ], $productAttributes, $variant_options);

            $this->product_attribute_repository->syncAttributes($product_attributes, $variant, $scope, $request, "store");

            array_map(function($child_attribute) use($variant) {
                AttributeConfigurableProduct::updateOrCreate(array_merge($child_attribute, [ "product_id" => $variant->id ]));
            }, $this->configurable_attributes);

            $variant->channels()->sync($request->get("channels"));
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
