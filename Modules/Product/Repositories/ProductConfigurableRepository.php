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
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductImage;

class ProductConfigurableRepository extends BaseRepository
{
    protected $attribute;

    public function __construct(Product $product, Attribute $attribute)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "website_id" => "required|exists:websites,id",
            "sku" => "required|unique:products,sku",
            "attribute_set_id" => "required|exists:attribute_sets,id",
        ];
        $this->attribute = $attribute;
    }

    public function validateAttributes(object $request): array
    {
        try
        {
            $attributes = $this->attribute::all();

            $product_attributes = [];
            foreach ( $request->get("attributes") as $product_attribute)
            {
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
                $product_attributes[] = array_merge($product_attribute, ["value_type" => $attribute_type], $validator->valid());
            }
            $product_attributes = $this->unsetter($product_attributes);
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
       catch ( Exception $exception )
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
                "sku" => \Str::slug($product->sku) . "-variant-" . implode("-", $permutation)."-".$product->id,
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
            $this->sku($product, $request, $method);
            $this->catalogInventory($product, $request, $method);
            $this->status($product, $request);
            $this->categories($product, $request);
            $this->images($product, $request);
        }
        catch ( Exception $exception )
        {
            throw $exception;  
        }

        return true;
    }

    public function unsetter(array $product_attributes): array
    {
        try
        {
            foreach ( $product_attributes as $key => $product_attribute )
            {
                if ( in_array($product_attribute["attribute_id"], Attribute::attributeMapper()) ) unset($product_attributes[$key]);
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
            $categories = explode(",", $this->getValue($request, "category_ids"));
            
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

            if ( is_array($images) )
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

}
