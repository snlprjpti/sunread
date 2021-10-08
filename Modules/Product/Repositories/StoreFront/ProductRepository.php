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

class ProductRepository extends BaseRepository
{
    public $search_repository, $categoryRepository, $page_groups, $config_fields, $count, $mainAttribute, $nested_product, $config_products;

    public function __construct(Product $product, ProductSearchRepository $search_repository, CategoryRepository $categoryRepository)
    {
        $this->model = $product;
        $this->search_repository = $search_repository;
        $this->categoryRepository = $categoryRepository;
        $this->model_name = "Product";
        $this->page_groups = ["hero_banner", "usp_banner_1", "usp_banner_2", "usp_banner_3"];
        $this->config_fields = config("category.attributes");
        $this->count = 0;
        $this->mainAttribute = [ "name", "sku", "type", "url_key", "quantity", "visibility", "price", "special_price", "special_from_date", "special_to_date", "short_description", "description", "meta_title", "meta_keywords", "meta_description", "new_from_date", "new_to_date"];
        $this->nested_product = [];
        $this->config_products = [];
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
                    $data["product_builder"] = $product->productBuilderValues()->whereScope("store")->whereScopeId($store->id)->get()->map(function($builder) {
                        return [
                            "component" => $builder->attribute,
                            "attributes" => $builder->value->map,
                            "position" => $builder->position
                        ];
                    })->toArray();
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
                    else $data[$attribute->slug] = $values?->name;
                    continue;
                }

                if(!in_array($attribute->slug, $this->mainAttribute)) $data["attributes"][$attribute->slug] = $values;
                else $data[$attribute->slug] = $values;

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

            if(isset($fetched["price"])) $fetched["price_formatted"] = isset($fetched["price"]) ? PriceFormat::get($fetched["price"], $store->id, "store") : null;

            if(isset($fetched["new_from_date"]) && isset($fetched["new_to_date"])) { 
                if(isset($fetched["new_from_date"])) $fromNewDate = date('Y-m-d H:m:s', strtotime($fetched["new_from_date"]));
                if(isset($fetched["new_to_date"])) $toNewDate = date('Y-m-d H:m:s', strtotime($fetched["new_to_date"])); 
                if(isset($fromNewDate) && isset($toNewDate)) $fetched["is_new_product"] = (($currentDate >= $fromNewDate) && ($currentDate <= $toNewDate)) ? 1 : 0;
                unset($fetched["new_from_date"], $fetched["new_to_date"]);
            }

            if ( $product->type == "configurable" || ($product->type == "simple" && isset($product->parent_id))) {
                if(isset($product->parent_id)) $product = $product->parent;
                $variant_ids = $product->variants->pluck("id")->toArray();
                $elastic_fetched = [
                    "_source" => ["show_configurable_attributes"],
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
                $fetched["configurable_products"] = $this->xyz;          
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
                $count = collect($attribute_values)->unique("id")->count(); 

                $j = 0;
                foreach($attribute_values as $attribute_value)
                {
                    $append_key = $key ? "$key.{$attribute_slug}.{$j}" : "{$attribute_slug}.{$j}";
                    $abc = [];
                    if(!isset($state[$attribute_value["id"]])) {
                        $state[$attribute_value["id"]] = true;
                        $abc["value"] = $attribute_value["id"];
                        $abc["label"] = $attribute_value["label"];
                        $abc["code"] = $attribute_value["code"];
                        if($attribute_value["attribute_slug"] == "color") {
                            $abc["url_key"] = $attribute_value["url_key"];
                            $abc["image"] = $attribute_value["image"];
                        }
                        if(count($attribute_slugs) == 1) {
                            $fake_array = array_merge($this->nested_product, [$attribute_value["attribute_slug"] => $attribute_value["id"]]);
                            $dot_product = collect($this->config_products)->where("attribute_combination", $fake_array)->first();
                            $abc["product_id"] = isset($dot_product["product_id"]) ? $dot_product["product_id"] : 0;
                            $abc["sku"] = isset($dot_product["product_sku"]) ? $dot_product["product_sku"] : 0;
                            $abc["stock_status"] = isset($dot_product["stock_status"]) ? $dot_product["stock_status"] : 0;
                            if($count == ($j+1)) $this->nested_product = [];
                        } 
                        setDotToArray($append_key, $this->xyz,  $abc);
                        $j = $j + 1;
                        if(count($attribute_slugs) > 1) {
                            $this->nested_product = [ $attribute_value["attribute_slug"] => $attribute_value["id"] ];
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

    Public function getBaseImage($product): ?array
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
