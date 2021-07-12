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
    protected $attribute, $attribute_set_repository, $channel_model, $store_model;
    
    public function __construct(Product $product, AttributeSetRepository $attribute_set_repository, AttributeRepository $attribute_repository, Channel $channel_model, Store $store_model)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "parent_id" => "sometimes|nullable|exists:products,id",
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "attribute_set_id" => "required|exists:attribute_sets,id",
            "website_id" => "required|exists:websites,id",
            "attributes" => "required|array",
            "scope" => "sometimes|in:global,website,channel,store"
        ];

        $this->attribute_set_repository = $attribute_set_repository;
        $this->attribute_repository = $attribute_repository;
        $this->channel_model = $channel_model;
        $this->store_model = $store_model;

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
            
            foreach ( $attributes as $attribute)
            {
                $product_attribute = [];
                if($this->scopeFilter($scope["scope"], $attribute->scope)) continue; 

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
            $all_product_attributes = $this->unsetter($all_product_attributes, $request_attribute_ids);
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
            foreach($data as $attribute) { 

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

    public function attributeMapperSync(object $product, object $request, string $method = "store"): bool
    {
        try
        {
            $this->sku($product, $request, $method);
            $this->status($product, $request);
            $this->categories($product, $request);
            $this->catalogInventory($product, $request, $method);
            $this->images($product, $request);
        }
        catch ( Exception $exception )
        {
            throw $exception;  
        }

        return true;
    }

    public function unsetter(array $product_attributes, array $requestIds): array
    {
        try
        {
            foreach ( $product_attributes as $key => $product_attribute )
            {
                if ( in_array($product_attribute["attribute_id"], Attribute::attributeMapper()) || !in_array($product_attribute["attribute_id"], $requestIds) ) unset($product_attributes[$key]);       
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $product_attributes;
    }

    public function getValue(object $request, string $attribute_mapper_slug): mixed
    {
        try
        {
            foreach ( $request["attributes"] as $attribute)
            {
                if (array_key_exists($attribute_mapper_slug, Attribute::attributeMapper()) && $attribute["attribute_id"] == Attribute::attributeMapper()[$attribute_mapper_slug]) $value = array_key_exists("value", $attribute) ? $attribute["value"] : null;
                continue;
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $value ?? null;
    }

    public function validataInventoryData(object $request): array
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

    public function catalogInventory(object $product, object $request, string $method = "store"): bool
    {
        DB::beginTransaction();
        try
        {  
            $is_in_stock = $this->getValue($request, "quantity_and_stock_status");
            
            if (isset($is_in_stock))
            {                
                $data = $this->validataInventoryData($request);
                $data["product_id"] = $product->id;
                $data["website_id"] = $product->website_id;
                $data["manage_stock"] = 1;
                $data["is_in_stock"] = (bool) $is_in_stock;
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
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        
        return true;
    }

    public function sku(object $product, object $request, string $method = "store" ): bool
    {
        DB::beginTransaction();

        try
        {
            $sku = $this->getValue($request, "sku");
            if (isset($sku))
            {
                $id = ($method == "update") ? $product->id : "";
                $validator = Validator::make(["sku" => $sku ], [
                    "sku" => "required|unique:products,sku,".$id
                ]);
    
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
                $product->update($validator->validated());
            }
        }
        catch ( Exception $exception )
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return true;
    }

    public function status(object $product, object $request): bool
    {
        DB::beginTransaction();

        try
        {
            $status = $this->getValue($request, "status");
            if (isset($status)) $product->update(["status" => $status]);
        }
        catch ( Exception $exception )
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return true;
    }

    public function categories(object $product, object $request): bool
    {
        DB::beginTransaction();

        try
        {
            $categories = $this->getValue($request, "category_ids");
            
            if (isset($categories) && !in_array("", $categories))
            {
                $validator = Validator::make(["categories" => $categories ], [
                    "categories" => "required|array",
                    "categories.*" => "required|exists:categories,id"
                ]);
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
    
                $product->categories()->sync($validator->validated()["categories"]);
            }
        }
        catch ( Exception $exception )
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return true;
    }

    public function images(object $product, object $request): bool
    {
        try
        {
            $this->storeImages($product, $this->getValue($request, "base_image"), "base_image");
            $this->storeImages($product, $this->getValue($request, "small_image"), "small_image");
            $this->storeImages($product, $this->getValue($request, "thumbnail_image"), "thumbnail_image");  
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true; 
    }

    public function storeImages(object $product, ?array $images, string $image_type): bool
    {
        DB::beginTransaction();

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
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return true;
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "website" && in_array($element_scope, ["global"])) return true;
        if($scope == "channel" && in_array($element_scope, ["global", "website"])) return true;
        if($scope == "store" && in_array($element_scope, ["global", "website", "channel"])) return true;
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
                        $attributesData = [
                            "id" => $attribute->id,
                            "name" => $attribute->name,
                            "slug" => $attribute->slug,
                            "type" => $attribute->type,
                            "scope" => $attribute->scope,
                            "position" => $attribute->position,
                            "is_required" => $attribute->is_required,
                            "use_default_value" =>  $existAttributeData ? 0 : 1
                        ];

                        $attributesData["value"] = $attribute->checkMapper() && !$attribute->checkOption() ? $this->getMapperValue($attribute, $product) : ($existAttributeData ? $existAttributeData->value->value : $this->getDefaultValues($product, $match));
                        

                        if(in_array($attribute->type, $this->attribute_repository->non_filterable_fields)) $attributesData["options"] = $this->attribute_set_repository->getAttributeOption($attribute); 
                        return $attributesData;
                    })->toArray()
                ];
            })->toArray();
        }
        catch( Exception $exception )
        {
            throw $exception;
        }
        return array_merge($product->toArray(), [
            "groups" => $groups
        ]);
    }

    public function getDefaultValues(object $product, array $data): mixed
    {
        if($data["scope"] != "global")
        {
            $input["attribute_id"] = $data["attribute_id"];
            $input["product_id"] = $product->id;
            switch($data["scope"])
            {
                case "store":
                    $input["scope"] = "channel";
                    $input["scope_id"] = $this->store_model->find($data["scope_id"])->channel->id;
                    break;
                
                case "channel":
                    $input["scope"] = "website";
                    $input["scope_id"] = $this->channel_model->find($data["scope_id"])->website->id;
                    break;

                case "website":
                    $input["scope"] = "global";
                    $input["scope_id"] = 0;
                    break;
            }
            return ($item = $product->product_attributes()->where($input)->first()) ? $item->value->value : (( $input["scope"] == "global") ? null : $this->getDefaultValues($product, $input));           
        }
        return null;
    }

    public function getMapperValue($attribute, $product)
    {
        if($attribute->slug == "sku") return $product->sku;
        if($attribute->slug == "status") return $product->status;
        if($attribute->slug == "category_ids") return $product->categories()->pluck('category_id')->toArray();
        if($attribute->slug == "base_image") return $product->images()->where('main_image', 1)->pluck('path')->toArray();
        if($attribute->slug == "small_image") return $product->images()->where('small_image', 1)->pluck('path')->toArray();
        if($attribute->slug == "thumbnail_image") return $product->images()->where('thumbnail', 1)->pluck('path')->toArray();
        if($attribute->slug == "quantity_and_stock_status") return $product->catalog_inventories()->first()->is_in_stock;
    }

}
