<?php

namespace Modules\Product\Repositories\StoreFront;

use Exception;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\AttributeOptionsChildProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Exceptions\ProductNotFoundIndividuallyException;

class ProductRepository extends BaseRepository
{

	public function __construct(Product $product)
	{
		$this->model = $product;
		$this->model_name = "Product";
	}

	public function productDetail(object $request, string $sku)
	{
		try
		{
			$website = Website::whereHostname($request->header("hc-host"))->firstOrFail();
            $channel = Channel::whereWebsiteId($website->id)->whereCode($request->header("hc-channel"))->firstOrFail();
            $store = Store::whereChannelId($channel->id)->whereCode($request->header("hc-store"))->firstOrFail();
			$request->sf_store = $store;
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
            $product = Product::whereWebsiteId($website->id)->whereSku($sku)->whereStatus(1)->with($relations)->firstOrFail();
			$data = [];
            $data["id"] = $product->id;
            $data["sku"] = $product->sku;
            
            $product_details = $product->product_attributes->mapWithKeys( function ($product_attribute) use ($store, $product){
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $product_attribute->attribute->id
                ];
                if ( $product_attribute->attribute->slug == "visibility" ) {
                    if ( $product->value($match)?->name == "Not Visible Individually" ) throw new ProductNotFoundIndividuallyException();
                }
                return (!$product_attribute->attribute->is_user_defined) ? [ $product_attribute->attribute->slug => ($product_attribute->attribute->type == "select") ? $product->value($match)?->name : $product->value($match) ] : [];
            })->toArray();
            $data = array_merge($data, $product_details);
            
            if ($product->type == "simple") {
                $data["attribute_options"] = $product->product_attributes->filter(function ($product_attribute) {
                    return ($product_attribute->attribute->slug == "color") || ($product_attribute->attribute->slug == "size");
                })->map( function ($product_attribute) use ($store, $product) {
                    $match = [
                        "scope" => "store",
                        "scope_id" => $store->id,
                        "attribute_id" => $product_attribute->attribute_id
                    ];
                    $attribute_options = [
                        "name" => $product_attribute->attribute->name,
                        "slug" => $product_attribute->attribute->slug,
                        "value" => $product->value($match),    
                    ];
                    if ($product->parent_id) {
                        $other_variant_ids = $product->parent->variants->where("id", "<>", $product->id)->pluck("id")->toArray();
                        $variant_attribute_options = AttributeOptionsChildProduct::whereIn("id", $other_variant_ids)->with(["attribute_option"])->get();
                        $options["possible_options"] = $variant_attribute_options->filter( fn ($variant_attribute_option) => $variant_attribute_option->attribute_option->attribute_id == $product_attribute->attribute_id)
                        ->unique("attribute_option_id")
                        ->map( function ($other_variant_option) { 
                            return [
                                "option_id" => $other_variant_option->attribute_option_id,
                                "name" => $other_variant_option->attribute_option->name,
                                "code" => $other_variant_option->attribute_option->code,
                                
                            ]; 
                        })->toArray();
                    }
                    return array_merge($attribute_options, $options);
                })->toArray();
            }
            $data["categories"] = CategoryResource::collection($product->categories);

            if ( $product->type == "configurable") {
                $data["extension_attributes"] = [
                   "attribute_configurable_products" => $product->attribute_configurable_products->map(function ($attribute_configurable_product) use ($product, $store) {
                    $variant_ids = $product->variants->pluck("id")->toArray();
                    $attribute_option_child_products = AttributeOptionsChildProduct::whereIn("product_id", $variant_ids)->with(["attribute_option.attribute", "variant_product.product_attributes.attribute", "variant_product.catalog_inventories"])->get();
                    $initial_attribute_id = $attribute_configurable_product->attribute_id;
                    $attribute_configurable_products = [
                        "attribute_id" => $attribute_configurable_product->attribute_id,
                        "title" => $attribute_configurable_product->attribute->name,
                        "slug" => $attribute_configurable_product->attribute->name,
                        "values" => $attribute_option_child_products->filter(function ($attribute_option_child_product) use ($attribute_configurable_product) {
                                return $attribute_option_child_product->attribute_option->attribute_id == $attribute_configurable_product->attribute_id;
                            })->unique("attribute_option_id")
                            ->map(function ($attribute_option_child_product) use ($attribute_option_child_products, $initial_attribute_id, $store, &$initial_attribute_option_id) {
                                $initial_attribute_option_id = $attribute_option_child_product->attribute_option_id; 
                                return [
                                    "option_id" => $attribute_option_child_product->attribute_option_id,
                                    "title" => $attribute_option_child_product->attribute_option->name,
                                    "code" => $attribute_option_child_product->attribute_option->code,
                                    "bundle_products" => $attribute_option_child_products
                                    // ->where("attribute_id", "<>", $initial_attribute_id)
                                    ->unique("attribute_id")
                                    ->map( function ($attribute_bundle_product) use ($attribute_option_child_products, $initial_attribute_option_id, $store) {
                                        return [
                                            "attribute_id" => $attribute_bundle_product->attribute_option->attribute_id,
                                            "name" => $attribute_bundle_product->attribute_option->attribute->name,
                                            "slug" => $attribute_bundle_product->attribute_option->attribute->slug,
                                            "link_products" => $attribute_option_child_products
                                            ->where("attribute_option_id", $initial_attribute_option_id)
                                            ->map( function ($link_product) use ($store) {
                                                $attribute_slugs = ["name", "special_price", "special_from_date", "special_to_date",];
                                                return array_merge([
                                                    "sku" => $link_product->variant_product->sku,
                                                    "option_id" => $link_product->attribute_option_id,], $link_product->variant_product
                                                    ->product_attributes
                                                    ->filter(fn ($product_attribute) => in_array($product_attribute->attribute->slug, $attribute_slugs))
                                                    ->mapWithKeys( function ($product_detail_attribute) use ($store, $link_product) {
                                                        $match = [
                                                            "scope" => "store",
                                                            "scope_id" => $store->id,
                                                            "attribute_id" => $product_detail_attribute->attribute_id 
                                                        ];
                                                        return [ $product_detail_attribute->attribute->slug => $link_product->variant_product->value($match) ];
                                                    })->toArray(), ["stock_items" => ["qty" =>  $link_product->variant_product->catalog_inventories->first()?->quantity, "is_in_stock" => (bool) $link_product->variant_product->catalog_inventories->first()?->is_in_stock ]]);
                                            })->toArray()
                                        ];
                                    })->toArray(),
                                ];
                            })->toArray(),
                        "possible_options" => $attribute_option_child_products->filter(fn ($attribute_option) => $attribute_option->attribute_option->attribute->id !== $initial_attribute_id)
                        ->unique("attribute_option_id")
                        ->map( function ($possible_attribute_option) {
                            return [
                                "option_id" => $possible_attribute_option->attribute_option_id,
                                "name" => $possible_attribute_option->attribute_option->name,
                                "code" => $possible_attribute_option->attribute_option->code
                            ];
                        })->toArray()
                       ];
                    return $attribute_configurable_products;
                   })->toArray(),             
                ];            
            }
            $inventory = $product->catalog_inventories->first();
            if ($product->type == "simple") $data["stock_items"] = ["qty" =>  $inventory?->quantity ?? 0.00, "is_in_stock" => (bool) $inventory?->is_in_stock ];

            $data["custom_attribute"] =  $product->product_attributes->mapWithKeys( function ($product_attribute) use ($store) {
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $product_attribute->attribute->id
                ];
                return ($product_attribute->attribute->is_user_defined && $product_attribute->attribute->is_visible_on_storefront ) ? [ $product_attribute->attribute->slug => $product_attribute->value($match) ] : [];
            })->toArray();

            $product_images = [];
            $product->images->map( function ($image) use (&$product_images){
                $images = $image->types->map( function ($type) use ($image) {
                    return [ "type" => ($type->slug == "gallery") ? "normal" : $type->slug, "path" => $image->path];
                })->toArray();
                $product_images = array_merge($product_images, $images);
            })->toArray();
            $data["gallery"] = $product_images;
		}
		catch (Exception $exception)
		{
			throw $exception;
		}

		return $data;
	}

}