<?php

namespace Modules\Product\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Core\Entities\Store;
use Modules\Core\Rules\ScopeRule;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Channel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Intervention\Image\Facades\Image;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\ProductImage;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Product\Entities\ProductAttribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Attribute\Repositories\AttributeSetRepository;
use Modules\Product\Entities\AttributeConfigurableProduct;
use Modules\Product\Entities\ImageType;
use Modules\Product\Jobs\MapProductImageTypeValueJob;
use Modules\Product\Rules\WebsiteWiseScopeRule;

class ProductRepository extends BaseRepository
{
    protected $attribute, $attribute_set_repository, $channel_model, $store_model, $image_repository;
    
    public function __construct(Product $product, AttributeSetRepository $attribute_set_repository, AttributeRepository $attribute_repository, ProductImageRepository $image_repository,Channel $channel_model, Store $store_model)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "parent_id" => "sometimes|nullable|exists:products,id",
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "attributes" => "required|array",
            "scope" => "sometimes|in:website,channel,store",
            "website_id" => "required|exists:websites,id"
        ];

        $this->attribute_set_repository = $attribute_set_repository;
        $this->attribute_repository = $attribute_repository;
        $this->channel_model = $channel_model;
        $this->store_model = $store_model;
        $this->image_repository = $image_repository;
    }

    public function validataInventoryData(array $data): array
    { 
        try
        {
            $config_rules = (isset($data["manage_stock"]) && $data["manage_stock"] == 1) ? 0 : 1;
            $no_config_rules = (isset($data["use_config_manage_stock"]) && $data["use_config_manage_stock"] == 1) ? 0 : 1;

            $validator = Validator::make($data, [
                "quantity" => "required|numeric",
                "use_config_manage_stock" => "required|boolean|in:$config_rules",
                "manage_stock" => "required|boolean|in:$no_config_rules"
            ]);
            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $validator->validated();
    }

    public function catalogInventory(object $product, object $request, string $method, array $value): bool
    {
        try
        {  
            if (isset($value["value"]) && isset($value["catalog_inventory"]))
            {                
                $data = $this->validataInventoryData($value["catalog_inventory"]);
                $data["product_id"] = $product->id;
                $data["website_id"] = $product->website_id;
                $data["is_in_stock"] = (bool) $value["value"];
                $match = [
                    "product_id" => $product->id,
                    "website_id" => $product->website_id
                ];

                unset($data["quantity"]); 
                $catalog_inventory = CatalogInventory::updateOrCreate($match, $data);
      
                $original_quantity = (float) $catalog_inventory->quantity;
                $adjustment_type = (($value["catalog_inventory"]["quantity"] - $original_quantity) > 0) ? "addition" : "deduction";
                LogCatalogInventoryItem::dispatchSync([
                    "product_id" => $catalog_inventory->product_id,
                    "website_id" => $catalog_inventory->website_id,
                    "event" => ($method == "store") ? "{$this->model_key}.store" : "{$this->model_key}.{$adjustment_type}",
                    "adjustment_type" => ($method == "store") ? "addition" : $adjustment_type,
                    "adjusted_by" => auth()->guard("admin")->id(),
                    "quantity" => ($method == "store") ? $value["catalog_inventory"]["quantity"] : (float) abs($original_quantity - $value["catalog_inventory"]["quantity"])
                ]);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        
        return true;
    }

    public function sku(object $product, object $request, string $method, array $value): bool
    {
        try
        {
            if (isset($value["value"]))
            {
                $id = ($method == "update") ? $product->id : "";
                $validator = Validator::make(["sku" => $value["value"] ], [
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

    public function status(object $product, object $request, string $method, array $value): bool
    {
        try
        {
            if (isset($value["value"]))
            {
                $attributeOption = AttributeOption::find($value["value"]);
                if($attributeOption) $product->update(["status" => $attributeOption?->code]);
            } 
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        return true;
    }

    public function categories(object $product, object $request, string $method, array $value): bool
    {
        try
        {           
            if (isset($value["value"]))
            {
                $validator = Validator::make(["categories" => $value["value"]], [
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

    public function gallery(object $product, object $request, string $method, array $value): bool
    {
        try
        {
            $request_images = $value["value"];

            if ($method == "update" && isset($request_images["existing"])) {
                $this->updateImageType($request_images, $product);
            }

            unset($request_images["existing"]);

            if ( !empty($request_images) ) {
                $validator = Validator::make($request_images, [
                    "*.type" => "required|array",
                    "*.type.*" => "in:base_image,thumbnail_image,section_background_image,small_image,gallery",
                    "*.file" => "required|mimes:bmp,jpeg,jpg,png",
                ],[
                    "*.type.*.in" => "Product Image type must be in base_image,thumbnail_image,section_background_image,small_image,gallery",
                ]);
                if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());    
               
                foreach ( $request_images as $image_values )
                {
                   
                    $this->storeImages($product, $image_values["file"], array_unique($image_values["type"]));
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true; 
    }

    private function updateImageType(mixed $data, object $product): bool
    {
        try
        {
            if (isset($data["existing"])) {
                $validate_data = $this->validateUpdateImage($data["existing"], $product);

                foreach ( $validate_data as $item )
                {
                    if ($item["delete"]) {
                        $this->image_repository->delete($item["id"], function ($deleted) {
                            if ($deleted->path) Storage::delete($deleted->path);
                        });
                        continue;
                    }
                    $product_image = ProductImage::whereId($item["id"])->first();
                    $product_image->types()->detach($product_image);
                    $image_type_ids = ImageType::whereIn("slug", $item["type"])->pluck("id")->toArray();
                    $product_image->types()->sync($image_type_ids);
                }
            }
        }
        catch (Exception $exception) 
        {
            throw $exception;
        }
        return true;
    }

    private function validateUpdateImage(array $data, object $product)
    {
        try
        {
            $validator = Validator::make($data, [
                "*.type" => "required|array",
                "*.type.*" => "in:base_image,thumbnail_image,section_background_image,small_image,gallery",
                "*.delete" => "required|boolean",
                "*.id" => "required|exists:product_images,id",
                "*.id" => Rule::in($product->images()->pluck("id")->toArray()),
            ], [
                "*.id.required" => "Product Image id is required",
                "*.id.in" => "Product Image id does not belongs to current product.",
                "*.type.*.in" => "Product Image type must be in base_image,thumbnail_image,section_background_image,small_image,gallery"
            ]);

            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $validator->validate();
    }

    public function storeImages(object $product, mixed $image, array $image_types): bool
    {
        try
        {
            if ( isset($image) ) {
                $key = Str::random(6);
                $data = [];
                $file_name = $this->generateFileName($image);
                $data["path"] = $image->storeAs("images/products/{$key}", $file_name);

                foreach ( $image_types as $image_type ) {
                    $image_dimensions = config("product_image.image_dimensions.product_{$image_type}");
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
                }
                $data["product_id"] = $product->id;
                $product_image = ProductImage::create($data);

                $image_type_ids = ImageType::whereIn("slug", $image_types)->pluck("id")->toArray();
                $product_image->types()->sync($image_type_ids);
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

    public function product_attribute_data(int $id, object $request): array
    {
        try
        {
            $product = $this->model::findOrFail($id);

            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope), new WebsiteWiseScopeRule($request->scope ?? "website", $product->website_id)]
            ]);
    
            $scope = [
                "scope" => $request->scope ?? "website",
                "scope_id" => $request->scope_id ??  $product->website_id,
            ];
    
            $fetched = [];
            $fetched = [
                "parent_id" => $product->id,
                "website_id" => $product->website_id
            ];
            $fetched["attributes"] = $this->getData($id, $scope);

            if ($product->type == "configurable") {
                $configurable_childs = $this->model->with("attribute_options_child_products")->whereParentId($id)->get();

                foreach($configurable_childs as $configurable_child)
                {
                    $fetched["configurable_attributes"][] = $configurable_child->attribute_options_child_products->map(function ($configurable_attribute) {      
                        return [
                            "product_id" => $configurable_attribute->product_id,
                            "attribute_id" => AttributeOption::find($configurable_attribute->attribute_option_id)?->attribute_id,
                            "attribute_option_id" => $configurable_attribute->attribute_option_id
                        ];
                    })->toArray();
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        
        return $fetched;
    }
    
    public function getParentScope(array $scope): array
    {
        try
        {
            switch($scope["scope"])
            {
                case "store":
                    $data["scope"] = "channel";
                    $data["scope_id"] = $this->store_model->find($scope["scope_id"])->channel->id;
                    break;
                    
                case "channel":
                    $data["scope"] = "website";
                    $data["scope_id"] = $this->channel_model->find($scope["scope_id"])->website->id;
                    break;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
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
                            "is_required" => $attribute->is_required,
                            "is_user_defined" => (bool) $attribute->is_user_defined
                        ];
                        if($match["scope"] != "website") $attributesData["use_default_value"] = $mapper ? 0 : ($existAttributeData ? 0 : 1);
                        $attributesData["value"] = $mapper ? $this->getMapperValue($attribute, $product) : ($existAttributeData ? $existAttributeData->value?->value : $this->getDefaultValues($product, $match));

                        if(in_array($attribute->type, $this->attribute_repository->non_filterable_fields))
                        {
                            $attributesData["options"] = $this->attribute_set_repository->getAttributeOption($attribute); 
                            if($attributesData["value"] && !is_array($attributesData["value"])) $attributesData["value"] = json_decode($attributesData["value"]);
                        } 
                        if($attribute->slug == "quantity_and_stock_status") $attributesData["children"] = $this->attribute_set_repository->getInventoryChildren($product->id);
                        
                        return $attributesData;
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
        $attribute = Attribute::findorFail($data["attribute_id"]);
        if(in_array($attribute->type, $this->attribute_repository->non_filterable_fields)) $attributeOptions = AttributeOption::whereAttributeId($attribute->id)->first();
        $defaultValue = isset($attributeOptions) ? $attributeOptions->id : $attribute->default_value;

        if($data["scope"] != "website")
        {
            $parent_scope = $this->getParentScope($data);
            $data["scope"] = $parent_scope["scope"];
            $data["scope_id"] = $parent_scope["scope_id"];
            $data["product_id"] = $product->id;
            return ($item = $product->product_attributes()->where($data)->first()) ? $item->value?->value : $this->getDefaultValues($product, $data);           
        }
        return ($item = $product->product_attributes()->where($data)->first()) ? $item->value?->value : $defaultValue;
    }

    public function getMapperValue($attribute, $product)
    {
        if($attribute->slug == "sku") return $product->sku;
        if($attribute->slug == "status")
        {
            $statusOption = AttributeOption::whereAttributeId($attribute->id)->whereCode($product->status)->first();
            return $statusOption?->id;
        } 
        if($attribute->slug == "category_ids") return $product->categories()->pluck('category_id')->toArray();
        if($attribute->slug == "gallery") return $this->getImages($product);

        if($attribute->slug == "quantity_and_stock_status") return ($data = $product->catalog_inventories()->first()) ? $data->is_in_stock : null;
    }

    private function getImages(object $product): array
    {
        try
        {
            $image_arr = $product->images()->get()->map(function ($image) {
                return [ "id" => $image->id, "type" => $image->types()->pluck("slug")->toArray(), "delete" => 0, "url" => Storage::url($image->path) ];
            })->toArray();

            $images = ["existing" => $image_arr ];   
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $images;
    }

    public function getFilterProducts(object $request): mixed
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope)],
                "website_id" => "required|exists:websites,id"
            ]);

            $this->validateListFiltering($request);
            
            $product = Product::whereWebsiteId($request->website_id);

            $validator = Validator::make( $request->all(), [
                "product_name" => "sometimes|string",
                "sku" => "sometimes|string",
                "attribute_set_id" => "sometimes|exists:attribute_sets,id",
                "status" => "sometimes|boolean",
                "visibility" => "sometimes",
                "type" => "sometimes|in:simple,configurable",
                "price_from" => "sometimes|decimal",
                "price_to" => "sometimes|decimal",
                "id_from" => "sometimes|numeric",
                "id_to" => "sometimes|numeric"
            ]);

            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());    

            if (isset($request->product_name))
            {
                $product_attributes = ProductAttribute::whereAttributeId(1)
                    ->whereScope($request->scope ?? "website")
                    ->whereScopeId($request->scope_id ?? $request->website_id)
                    ->get();

                $product_ids = [];
                foreach ( $product_attributes as $product_attribute )
                {
                    $value = $product_attribute->value()->query();
                    $matched = $value->whereLike("value", $request->product_name)->get();
                    if(count($matched) > 0) $product_ids[] = $product_attribute->product()->pluck("id");
                }
                $product->whereIn("id", Arr::flatten($product_ids));
            }

            if (isset($request->visibility))
            {
                $product_attributes = ProductAttribute::whereAttributeId(1)
                    ->whereScope($request->scope ?? "website")
                    ->whereScopeId($request->scope_id ?? $request->website_id)
                    ->get();

                $product_ids = [];
                foreach ( $product_attributes as $product_attribute )
                {
                    $value = $product_attribute->value()->query();
                    $matched = $value->where("value", $request->visibility)->get();
                    if(count($matched) > 0) $product_ids[] = $product_attribute->product()->pluck("id"); 
                }
                $product->whereIn("id", Arr::flatten($product_ids));
            }

            if (isset($request->price_from) || isset($request->price_to))
            {
                $product_attributes = ProductAttribute::whereAttributeId(3)
                    ->whereScope($request->scope ?? "website")
                    ->whereScopeId($request->scope_id ?? $request->website_id)
                    ->get();

                $product_ids = [];
                foreach ( $product_attributes as $product_attribute )
                {
                    $value = $product_attribute->value()->query();
                    $matched = $value->whereBetween("value", [$request->price_from ?? 0, $request->price_to])->get();
                    if(count($matched) > 0) $product_ids[] = $product_attribute->product()->pluck("id");
                }
                $product->whereIn("id", Arr::flatten($product_ids));
            }

            if (isset($request->id_from) || isset($request->id_to))
            {
                $product->whereBetween("id", [$request->id_from ?? 0, $request->id_to]);
            }

            if (isset($request->type)) $product->where("type", $request->type); 

            if (isset($request->sku)) $product->whereLike("sku", $request->sku);

            if (isset($request->attribute_set_id)) $product->where("attribute_set_id", $request->attribute_set_id);

            if (isset($request->status)) $product->where("status",$request->status);

        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $product;
    }

}
