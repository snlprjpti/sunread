<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Validator;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Attribute\Repositories\AttributeSetRepository;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductImage;

class ProductRepository extends BaseRepository
{
    protected $attribute, $attribute_set_repository, $channel_model, $store_model, $attributeMapperSlug, $functionMapper, $non_required_attributes;
    
    public function __construct(Product $product, AttributeSetRepository $attribute_set_repository, AttributeRepository $attribute_repository, Channel $channel_model, Store $store_model)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "parent_id" => "sometimes|nullable|exists:products,id",
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "attributes" => "required|array",
            "scope" => "sometimes|in:website,channel,store"
        ];

        $this->attribute_set_repository = $attribute_set_repository;
        $this->attribute_repository = $attribute_repository;
        $this->channel_model = $channel_model;
        $this->store_model = $store_model;
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

    }

    public function validateAttributes(object $product, object $request, array $scope, ?string $product_type = null): array
    {
        try
        {
            $attribute_set = AttributeSet::whereId($product->attribute_set_id)->firstOrFail();
            
            $attributes = $attribute_set->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes;
            })->first();

            $request_attribute_ids = array_map( function ($request_attribute) {
                return $request_attribute["attribute_id"];
            }, $request->get("attributes"));

            $request_attribute_collection = collect($request->get("attributes"));

            $all_product_attributes = [];

            if($product_type) $super_attributes = Arr::pluck($request->super_attributes, 'attribute_id');
            
            foreach ( $attributes as $attribute)
            {
                $product_attribute = [];
                if($this->scopeFilter($scope["scope"], $attribute->scope)) continue; 
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

                $all_product_attributes[] = array_merge($product_attribute, ["value_type" => $attribute_type], $validator->valid()); 
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return collect($all_product_attributes)->where("value", "!=", null)->toArray();
    }

    public function syncAttributes(array $data, object $product, array $scope, object $request, string $method = "store", ?string $product_type = null): bool
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.attibutes.sync.before");

        try
        {
            foreach($data as $attribute) { 

                if($product_type && in_array($attribute['attribute_slug'], $this->non_required_attributes)) continue;

                if( in_array($attribute["attribute_slug"], $this->attributeMapperSlug) )
                {
                    $function_name = $this->functionMapper[$attribute["attribute_slug"]];
                    $this->$function_name($product, $request, $method, $attribute["value"]);
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

    private function validataInventoryData(object $request): array
    { 
        try
        {
            $validator = Validator::make($request->all(), [
                "catalog_inventory.quantity" => "required|decimal",
                "catalog_inventory.use_config_manage_stock" => "required|boolean"
            ]);
            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $validator->validated()["catalog_inventory"];
    }

    private function catalogInventory(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {  
            if ($value)
            {                
                $data = $this->validataInventoryData($request);
                $data["product_id"] = $product->id;
                $data["website_id"] = $product->website_id;
                $data["manage_stock"] = 1;
                $data["is_in_stock"] = (bool) $value;
                $match = [
                    "product_id" => $product->id,
                    "website_id" => $product->website_id
                ];

                unset($data["quantity"]); 
                $catalog_inventory = CatalogInventory::updateOrCreate($match, $data);
      
                $original_quantity = (float) $catalog_inventory->quantity;
                $adjustment_type = (($request->catalog_inventory["quantity"] - $original_quantity) > 0) ? "addition" : "deduction";
                LogCatalogInventoryItem::dispatchSync([
                    "product_id" => $catalog_inventory->product_id,
                    "website_id" => $catalog_inventory->website_id,
                    "event" => ($method == "store") ? "{$this->model_key}.store" : "{$this->model_key}.{$adjustment_type}",
                    "adjustment_type" => ($method == "store") ? "addition" : $adjustment_type,
                    "adjusted_by" => auth()->guard("admin")->id(),
                    "quantity" => ($method == "store") ? $request->catalog_inventory["quantity"] : (float) abs($original_quantity - $request->catalog_inventory["quantity"])
                ]);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        
        return true;
    }

    private function sku(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {
            if ($value)
            {
                $id = ($method == "update") ? $product->id : "";
                $validator = Validator::make(["sku" => $value ], [
                    "sku" => "required|unique:products,sku,".$id
                ]);
    
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
                $product->update($validator->validated());
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }

    private function status(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {
            if($value) $product->update(["status" => $value]);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        return true;
    }

    private function categories(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {           
            if ($value)
            {
                $validator = Validator::make(["categories" => $value ], [
                    "categories" => "required|array",
                    "categories.*" => "required|exists:categories,id"
                ]);
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
    
                $product->categories()->sync($validator->validated()["categories"]);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }

    private function base_image(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {
            $this->storeImages($product, $value, "base_image");
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true; 
    }

    private function small_image(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {
            $this->storeImages($product, $value, "small_image");
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true; 
    }

    private function thumbnail_image(object $product, object $request, string $method, mixed $value): bool
    {
        try
        {
            $this->storeImages($product, $value, "thumbnail_image");
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true; 
    }

    private function storeImages(object $product, ?array $images, string $image_type): bool
    {
        try
        {
            if ( isset($images) && is_array($images) )
            {
                $data = [];
                $image_dimensions = config("product_image.image_dimensions.product_{$image_type}");
                $position = 1;

                $validator = Validator::make(["image" =>  $images], [
                    "image.*" => "required|mimes:bmp,jpeg,jpg,png"
                ]);
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
    
                foreach ( $images as $index => $image )
                {
                    $position += $index;
                    $key = \Str::random(6);
                    $file_name = $image->getClientOriginalName();
                    $data["path"] = $image->storeAs("images/products/{$key}", $file_name);
                    foreach ( $image_dimensions as $dimension )
                    {
                        $width = $dimension["width"];
                        $height = $dimension["height"];
                        $path = "images/products/{$key}/{$image_type}";
                        if(!Storage::has($path)) Storage::makeDirectory($path, 0777, true, true);
    
                        $image = Image::make($image)
                            ->fit($width, $height, function($constraint) {
                                $constraint->upsize();
                            })->encode('jpg', 80);
                    }
                    $data["position"] = $position;
                    $data["product_id"] = $product->id;
                    
                    switch ( $image_type )
                    {
                        case "base_image" :
                            $data["main_image"] = 1;
                        break;
    
                        case "small_image" :
                            $data["small_image"] = 1;
                        break;
    
                        case "thumbnail_image" :
                            $data["thumbnail"] = 1;
                        break;
                    }

                    ProductImage::create($data);
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "channel" && in_array($element_scope, ["website"])) return true;
        if($scope == "store" && in_array($element_scope, ["website", "channel"])) return true;
        return false;
    }

    public function getData(int $id, array $scope): array
    {
        try
        {
            $product = $this->model->with([ "parent", "brand", "website" ])->findOrFail($id);
        
            $attribute_set = AttributeSet::findOrFail($product->attribute_set_id);
    
            $groups = $attribute_set->attribute_groups->sortBy("position")->map(function ($attribute_group) use ($product, $scope)  { 
                return [
                    "id" => $attribute_group->id,
                    "name" => $attribute_group->name,
                    "position" => $attribute_group->position,
                    "attributes" => $attribute_group->attributes->map(function ($attribute) use ($product, $scope) {
                        $match = [
                            "attribute_id" => $attribute->id,
                            "scope" => $scope["scope"],
                            "scope_id" => $scope["scope_id"]
                        ];

                        $existAttributeData = $product->product_attributes()->where($match)->first();
                        $mapper = $attribute->checkMapper() && !$attribute->checkOption();

                        $attributesData = [
                            "id" => $attribute->id,
                            "name" => $attribute->name,
                            "slug" => $attribute->slug,
                            "type" => $attribute->type,
                            "scope" => $attribute->scope,
                            "position" => $attribute->position,
                            "is_required" => $attribute->is_required
                        ];
                        if($match["scope"] != "website") $attributesData["use_default_value"] = $mapper ? 0 : ($existAttributeData ? 0 : 1);
                        $attributesData["value"] = $mapper ? $this->getMapperValue($attribute, $product) : ($existAttributeData ? $existAttributeData->value->value : $this->getDefaultValues($product, $match));
                        

                        if(in_array($attribute->type, $this->attribute_repository->non_filterable_fields)) $attributesData["options"] = $this->attribute_set_repository->getAttributeOption($attribute); 
                        return $attributesData;
                    })->reject(function ($item) use($scope){
                        return $this->scopeFilter($scope["scope"], $item['scope']); 
                    })->toArray()
                ];
            })->toArray();
        }
        catch( Exception $exception )
        {
            throw $exception;
        }
        return $groups;
    }

    public function getDefaultValues(object $product, array $data): mixed
    {
        if($data["scope"] != "website")
        {
            $data["attribute_id"] = $data["attribute_id"];
            $data["product_id"] = $product->id;
            switch($data["scope"])
            {
                case "store":
                    $data["scope"] = "channel";
                    $data["scope_id"] = $this->store_model->find($data["scope_id"])->channel->id;
                    break;
                
                case "channel":
                    $data["scope"] = "website";
                    $data["scope_id"] = $this->channel_model->find($data["scope_id"])->website->id;
                    break;
            }
            return ($item = $product->product_attributes()->where($data)->first()) ? $item->value->value : $this->getDefaultValues($product, $data);           
        }
        return ($item = $product->product_attributes()->where($data)->first()) ? $item->value->value : null;
    }

    public function getMapperValue($attribute, $product)
    {
        if($attribute->slug == "sku") return $product->sku;
        if($attribute->slug == "status") return $product->status;
        if($attribute->slug == "category_ids") return $product->categories()->pluck('category_id')->toArray();
        if($attribute->slug == "base_image") return $product->images()->where('main_image', 1)->pluck('path')->toArray();
        if($attribute->slug == "small_image") return $product->images()->where('small_image', 1)->pluck('path')->toArray();
        if($attribute->slug == "thumbnail_image") return $product->images()->where('thumbnail', 1)->pluck('path')->toArray();
        if($attribute->slug == "quantity_and_stock_status") return ($data = $product->catalog_inventories()->first()) ? $data->is_in_stock : null;
    }

}
