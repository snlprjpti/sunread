<?php

namespace Modules\Product\Repositories\StoreFront;

use Exception;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Exceptions\CategoryNotFoundException;
use Modules\Category\Repositories\StoreFront\CategoryRepository;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\PriceFormat;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\AttributeOptionsChildProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Exceptions\ProductNotFoundIndividuallyException;
use Modules\Product\Repositories\ProductSearchRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\Page\Repositories\StoreFront\PageRepository;
use Modules\Tax\Facades\TaxPrice;

class ProductRepository extends BaseRepository
{
    public $search_repository, $categoryRepository, $page_groups, $config_fields, $count, $mainAttribute, $nested_product = [], $config_products = [], $pageRepository, $final_product_val = [];

    public function __construct(Product $product, ProductSearchRepository $search_repository, CategoryRepository $categoryRepository, PageRepository $pageRepository)
    {
        $this->model = $product;
        $this->search_repository = $search_repository;
        $this->categoryRepository = $categoryRepository;
        $this->model_name = "Product";
        $this->page_groups = ["hero_banner", "usp_banner_1", "usp_banner_2", "usp_banner_3"];
        $this->config_fields = config("category.attributes");
        $this->pageRepository = $pageRepository;
        $this->count = 0;
        $this->mainAttribute = [ "name", "sku", "type", "url_key", "quantity", "visibility", "price", "special_price", "special_from_date", "special_to_date", "short_description", "description", "meta_title", "meta_keywords", "meta_description", "new_from_date", "new_to_date", "animated_image", "disable_animation"];
    }

    public function productDetail(object $request, mixed $identifier, ?int $parent_identifier = null): ?array
    {
        try
        {
            $coreCache = $this->getCoreCache($request);
            $relations = [
                "catalog_inventories",
                "images",
                "images.types",
                "categories",
                "categories.values",
                "product_attributes",
                "product_attributes.attribute",
                "attribute_configurable_products",
                "attribute_configurable_products.attribute",
                "variants",
                "parent.variants.attribute_options_child_products.attribute_option",
                "parent.variants.product_attributes",
                "variants.attribute_configurable_products",
                "variants.attribute_configurable_products.attribute",
                "variants.attribute_configurable_products.attribute_option",
                "attribute_options_child_products"

            ];
            if(!$parent_identifier) {
                $product_attr = ProductAttribute::query()->with(["value"]);
                $attribute_id = Attribute::whereSlug("url_key")->first()?->id;
                $product_attr = $product_attr->whereAttributeId($attribute_id)->get()->filter( fn ($attr_product) => $attr_product->value->value == $identifier)->first();
                $product = Product::whereId($identifier)
                ->orWhere("id", $product_attr?->product_id)
                ->whereWebsiteId($coreCache->website->id)
                ->whereStatus(1)->with($relations)->firstOrFail();
            }
            else {
                $product = Product::whereId($identifier)
                ->whereParentId($parent_identifier)
                ->whereWebsiteId($coreCache->website->id)
                ->whereStatus(1)->with($relations)->firstOrFail();    
            }
            
            $store = $coreCache->store;

            $attribute_set = AttributeSet::where("id", $product->attribute_set_id)->first();
            $attributes = $attribute_set->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes;
            })->flatten(1);
            $data = [];

            foreach($attributes as $attribute)
            {
                if(in_array($attribute->slug, ["sku", "status", "quantity_and_stock_status", "has_weight", "weight", "cost", "category_ids"])) continue;

                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $attribute->id
                ];
                $values = $product->value($match);

                if ( !$parent_identifier && $attribute->slug == "visibility" && $values?->name == "Not Visible Individually" ) throw new ProductNotFoundIndividuallyException();

                if($attribute->slug == "gallery") {
                    $data["image"] = $this->getBaseImage($product);
                    $data["rollover_image"] = $this->getImages($product, "rollover_image");
                    $data["gallery"] = $product->images()->wherehas("types", function($query) {
                        $query->whereSlug("gallery");
                    })->get()->map(function ($gallery) {
                        return [
                            "url" => Storage::url($gallery->path),
                            "background_color" => $gallery->background_color
                        ];
                    })->toArray();
                    continue;
                }

                if($attribute->slug == "component") {
                    $product_builders = $product->productBuilderValues()->whereScope("store")->whereScopeId($store->id)->get();
                    if($product_builders->isEmpty()) $product_builders = $product->getBuilderParentValues($match);
                    
                    if($product_builders->isEmpty() && $product->parent_id) {
                        $product_builders = $product->parent->productBuilderValues()->whereScope("store")->whereScopeId($store->id)->get();
                        if($product_builders->isEmpty()) $product_builders = $product->parent->getBuilderParentValues($match);
                    }
                    $data["product_builder"] = $product_builders ? $this->pageRepository->getComponent($coreCache, $product_builders) : [];
                    continue;
                }

                if($product->type == "configurable" && ($attribute->slug == "price" ||  $attribute->slug == "special_price")) {
                    $first_variant = $product->variants->first();
                    $data[$attribute->slug] = $first_variant->value($match);
                    continue;
                }

                if(in_array($attribute->type, [ "select", "multiselect", "checkbox" ]))
                {
                    if ($values instanceof Collection) $data[$attribute->slug] = $values->pluck("name")->toArray();
                    else {
                        $data[$attribute->slug] = $values?->name;
                        if($attribute->slug == "tax_class_id") $data["tax_class"] = $values?->id;
                    }
                    continue;
                }

                if(!in_array($attribute->slug, $this->mainAttribute)) $data["attributes"][$attribute->slug] = $values;
                else $data[$attribute->slug] = $values;

                if(($attribute->type == "image" || $attribute->type == "file") && $values) $data[$attribute->slug] = Storage::url($values);

            }

            $fetched = $product->only(["id", "sku", "status", "website_id", "parent_id", "type"]);
            $inventory = $product->catalog_inventories()->select("quantity", "is_in_stock")->first()?->toArray();
            $fetched = array_merge($fetched, $inventory, $data);

            $today = date('Y-m-d');
            $currentDate = date('Y-m-d H:m:s', strtotime($today));
            
            if(isset($fetched["special_price"])) {
                if(isset($fetched["special_from_date"])) $fromDate = date('Y-m-d H:m:s', strtotime($fetched["special_from_date"]));
                if(isset($fetched["special_to_date"])) $toDate = date('Y-m-d H:m:s', strtotime($fetched["special_to_date"])); 
                if(!isset($fromDate) && !isset($toDate)) $fetched["special_price_formatted"] = PriceFormat::get($fetched["special_price"], $store->id, "store");
                else $fetched["special_price_formatted"] = (($currentDate >= $fromDate) && ($currentDate <= $toDate)) ? PriceFormat::get($fetched["special_price"], $store->id, "store") : null;
            }

            if(isset($fetched["price"])) {
                $calculateTax = TaxPrice::calculate($request, $fetched["price"], isset($fetched["tax_class"]) ? $fetched["tax_class"] : null);
                $fetched["tax_amount"] = $calculateTax?->tax_rate_value;
                $fetched["price"] += $fetched["tax_amount"];
            }

            $fetched["price_formatted"] = isset($fetched["price"]) ? PriceFormat::get($fetched["price"], $store->id, "store") : null;

            if(isset($fetched["new_from_date"]) && isset($fetched["new_to_date"])) { 
                if(isset($fetched["new_from_date"])) $fromNewDate = date('Y-m-d H:m:s', strtotime($fetched["new_from_date"]));
                if(isset($fetched["new_to_date"])) $toNewDate = date('Y-m-d H:m:s', strtotime($fetched["new_to_date"])); 
                if(isset($fromNewDate) && isset($toNewDate)) $fetched["is_new_product"] = (($currentDate >= $fromNewDate) && ($currentDate <= $toNewDate)) ? 1 : 0;
                unset($fetched["new_from_date"], $fetched["new_to_date"]);
            }

            if(isset($fetched["disable_animation"])  && isset($fetched["animated_image"])) {
                $fetched["animated_images"] = [
                    "animated_image" => $fetched["animated_image"],
                    "disable_animation" => $fetched["disable_animation"]
                ];
                unset($fetched["animated_image"], $fetched["disable_animation"] );
            }

            $this->nested_product = [];
            $this->config_products = [];
            $this->final_product_val = [];
            if ( $product->type == "configurable" || ($product->type == "simple" && isset($product->parent_id))) {
                if(isset($product->parent_id)) $product = $product->parent;
                $variant_ids = $product->variants->pluck("id")->toArray();
                $elastic_fetched = [
                    "_source" => ["show_configurable_attributes"],
                    "size" => count($variant_ids),
                    "query"=> [
                        "bool" => [
                            "must" => [
                                $this->search_repository->terms("id", $variant_ids)
                            ]
                        ]   
                    ],
                ]; 
                $elastic_data =  $this->search_repository->searchIndex($elastic_fetched, $store); 
                $elastic_variant_products = isset($elastic_data["hits"]["hits"]) ? collect($elastic_data["hits"]["hits"])->pluck("_source.show_configurable_attributes")->flatten(1)->toArray() : [];
                $this->config_products = $elastic_variant_products;

                $this->getVariations($elastic_variant_products);  
                $fetched["configurable_products"] = $this->final_product_val;          
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getVariations(array $elastic_variant_products, ?string $key = null)
    {
        try
        {
            $attribute_slugs = collect($elastic_variant_products)->pluck("attribute_slug")->unique()->values()->toArray();       
            foreach($attribute_slugs as $attribute_slug)
            {
                $state = [];
                
                $attribute_values = collect($elastic_variant_products)->where("attribute_slug", $attribute_slug)->values()->toArray();
                $variations = collect($elastic_variant_products)->where("attribute_slug", "!=", $attribute_slug)->values()->toArray();
                //$count = collect($attribute_values)->unique("id")->count(); 

                $j = 0;
                foreach($attribute_values as $attribute_value)
                {
                    $append_key = $key ? "$key.{$attribute_slug}.{$j}" : "{$attribute_slug}.{$j}";
                    $product_val_array = [];
                    if(!isset($state[$attribute_value["id"]])) {
                        $state[$attribute_value["id"]] = true;
                        $product_val_array["value"] = $attribute_value["id"];
                        $product_val_array["label"] = $attribute_value["label"];
                        $product_val_array["code"] = $attribute_value["code"];
                        if($attribute_value["attribute_slug"] == "color") {
                            $visibility_item = collect($attribute_values)->where("visibility", 8)->where("id", $attribute_value["id"])->first();
                            $product_val_array["url_key"] = isset($visibility_item["url_key"]) ? $visibility_item["url_key"] : $attribute_value["url_key"];
                            $product_val_array["image"] = isset($visibility_item["image"]) ? $visibility_item["image"] : $attribute_value["image"];
                        }
                        if(count($attribute_slugs) == 1) {
                            $fake_array = array_merge($this->nested_product, [$attribute_value["attribute_slug"] => $attribute_value["id"]]);
                            $dot_product = collect($this->config_products)->where("attribute_combination", $fake_array)->first();
                            $product_val_array["product_id"] = isset($dot_product["product_id"]) ? $dot_product["product_id"] : 0;
                            $product_val_array["sku"] = isset($dot_product["product_sku"]) ? $dot_product["product_sku"] : 0;
                            $product_val_array["stock_status"] = isset($dot_product["stock_status"]) ? $dot_product["stock_status"] : 0;
                            //if($count == ($j+1)) $this->nested_product = [];
                        } 
                        setDotToArray($append_key, $this->final_product_val,  $product_val_array);
                        $j = $j + 1;
                        if(count($attribute_slugs) > 1) {
                            $this->nested_product[ $attribute_value["attribute_slug"] ] = $attribute_value["id"];
                            $this->getVariations($variations, "{$append_key}.variations"); 
                        }  
                    }
                }
            }     
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function getBaseImage($product): ?array
    {
        try
        {
            $image = $product->images()->wherehas("types", function($query) {
                $query->whereSlug("base_image");
            })->first();
            if(!$image) $image = $product->images()->first();
            $path = $image ? Storage::url($image->path) : $image;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
         
        return [
            "url" => $path,
            "background_color" => $image?->background_color
        ];
    }

    public function getImages($product, $image_name): ?array
    {
        try
        {
            $image = $product->images()->wherehas("types", function($query) use($image_name) {
                $query->whereSlug($image_name);
            })->first();
            $path = $image ? Storage::url($image->path) : $image;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
         
        return [
            "url" => $path,
            "background_color" => $image?->background_color
        ];
    }

    public function getCategory(array $scope, string $category_slug): object
    {
        try
        {
            $category_value = CategoryValue::whereAttribute("slug")->whereValue($category_slug)->firstOrFail();
            $category = $category_value->category;

            if(!$this->categoryRepository->checkMenuStatus($category, $scope)) throw new CategoryNotFoundException();
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $category;  
    }

    public function getOptions(object $request, string $category_slug): ?array
    {
        try
        {
            $coreCache = $this->getCoreCache($request);
            $scope = [
                "scope" => "store",
                "scope_id" => $coreCache->store->id
            ]; 

            $category = $this->getCategory($scope, $category_slug);

            $fetched = $this->search_repository->getFilterOptions($category->id, $coreCache->store);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function categoryWiseProduct(object $request, string $category_slug): ?array
    {
        try
        {
            $fetched = [];

            $coreCache = $this->getCoreCache($request);
            $scope = [
                "scope" => "store",
                "scope_id" => $coreCache->store->id
            ]; 

            $category = $this->getCategory($scope, $category_slug);

            $fetched = $this->search_repository->getFilterProducts($request, $category->id, $coreCache->store);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getCategoryData(object $request, string $category_slug): ?array
    {
        try
        {
            $fetched = [];

            $coreCache = $this->getCoreCache($request);
            $scope = [
                "scope" => "store",
                "scope_id" => $coreCache->store->id
            ]; 

            $category = $this->getCategory($scope, $category_slug);

            $fetched["category"] = $this->getPages($category, $scope);
            if($category->parent_id) $parent = Category::findOrFail($category->parent_id);
            $fetched["navigation"] = $this->categoryRepository->getCategories(isset($parent) ? $parent->children : $category->children, $scope);
            $fetched["breadcrumbs"] = $this->getBreadCumbs($category, isset($parent) ? $parent : null); 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getPages(object $category, array $scope): array
    {
        try
        {
            $data = [];

            $data["id"] = $category->id;
            foreach(["name", "slug", "description"] as $key) $data[$key] = $category->value($scope, $key);
    
            foreach($this->page_groups as $group)
            {
                $item = [];
                $slugs = collect($this->config_fields[$group]["elements"])->pluck("slug");
                foreach($slugs as $slug)
                {
                    $item[$slug] = $category->value($scope, $slug);
                }
                $data["pages"][$group] = $item;
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function getBreadCumbs(object $category, ?object $parent): array
    {
        try
        {
            $breadcumbs = [];
            if($parent) $breadcumbs[] = new CategoryResource($parent);
            $breadcumbs[] = new CategoryResource($category);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $breadcumbs;
    }

    public function searchWiseProduct(object $request): ?array
    {
        try
        {
            $fetched = [];

            $coreCache = $this->getCoreCache($request);
            $scope = [
                "scope" => "store",
                "scope_id" => $coreCache->store->id
            ]; 

            $fetched = $this->search_repository->getSearchProducts($request, $coreCache->store);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

}
