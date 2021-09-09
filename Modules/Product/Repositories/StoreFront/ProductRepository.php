<?php

namespace Modules\Product\Repositories\StoreFront;

use Exception;
use Illuminate\Support\Arr;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\AttributeConfigurableProduct;
use Modules\Product\Entities\AttributeOptionsChildProduct;
use Modules\Product\Entities\Product;

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
                "variants.attribute_configurable_products",
                "variants.attribute_configurable_products.attribute",
                "variants.attribute_configurable_products.attribute_option",
                "attribute_options_child_products"

            ];
            $product = Product::whereWebsiteId($website->id)->whereSku($sku)->with($relations)->firstOrFail();

			$data = [];
            $data["id"] = $product->id;
            $data["sku"] = $product->sku;
            
            $product_details = $product->product_attributes->mapWithKeys( function ($product_attribute) use ($store, $product){
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $product_attribute->attribute->id
                ];
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
                    return [
                        "name" => $product_attribute->attribute->name,
                        "slug" => $product_attribute->attribute->slug,
                        "value" => $product->value($match),
                        "possible_options" => $product_attribute->attribute->attribute_options->pluck("name", "id")->toArray(),
                    ];
                })->toArray();
            }
            // $data["categories"] = CategoryResource::collection($product->categories);
            // dd($product->attribute_configurable_products);
            if ( $product->type == "configurable") {
                $data["extension_attributes"][] = [
                   "attribute_configurable_products" => $product->attribute_configurable_products->map(function ($attribute_configurable_product) use ($product) {
                    $variant_ids = $product->variants->pluck("id")->toArray();
                    $attribute_option_child_products = AttributeOptionsChildProduct::whereIn("product_id", $variant_ids)->with(["attribute_option", "variant_product"])->get();
                    // dd($attribute_option_child_products);
                    return [
                           "attribute_id" => $attribute_configurable_product->attribute_id,
                           "title" => $attribute_configurable_product->attribute->name,
                           "slug" => $attribute_configurable_product->attribute->name,
                           "values" => $attribute_option_child_products->filter(function ($attribute_option_child_product) use ($attribute_configurable_product) {
                                    return $attribute_option_child_product->attribute_option->attribute_id == $attribute_configurable_product->attribute_id;
                                })->unique("attribute_option_id")->map(function ($attribute_option_child_product) use ($attribute_option_child_products) {
                                    dd($attribute_option_child_products)  ;
                                    return [
                                        "option_id" => $attribute_option_child_product->attribute_option_id,
                                        "title" => $attribute_option_child_product->attribute_option->name,
                                        "code" => $attribute_option_child_product->attribute_option->code,
                                        "bundle_products" => []
                                    ];
                                })->toArray(),
                       ];
                   })->toArray(), 
                ];
                dd($data);

                $variant_ids = $product->variants->pluck("id")->toArray();
                $attribute_configurable_products = AttributeConfigurableProduct::whereIn("product_id", $variant_ids)
                ->with([ "product", "attribute", "attribute_option", "attribute_option.attribute"])->get();
                
                $configurable_products = [];
                foreach( $attribute_configurable_products->groupBy("attribute_id") as $index => $group_products )
                {
                    $initial_attribute_id = $group_products->first()?->attribute_id;
                    $configurable_products[] = [
                        "attribute_id" => $group_products->first()?->attribute_id,
                        "name" => $group_products->first()?->attribute->name,
                        "slug" => $group_products->first()?->attribute->slug,
                        "values" => $group_products->unique("attribute_option_id")->map(function ($group_product_value) use ($attribute_configurable_products, $initial_attribute_id) {
                            $initial_attribute_option_id = $group_product_value->attribute_option_id; 
                            return [
                                "option_id" => $group_product_value->attribute_option_id,
                                "name" => $group_product_value->attribute_option->name,
                                "code" => $group_product_value->attribute_option->code,
                                "bundle_product_options" => $attribute_configurable_products
                                ->where("attribute_id", "<>", $initial_attribute_id)
                                ->unique("attribute_id")
                                ->map( function ($bundle_product_option) use ($attribute_configurable_products, $initial_attribute_option_id, $initial_attribute_id) {
                                    return [
                                        "attribute_id" => $bundle_product_option->attribute_id,
                                        "name" => $bundle_product_option->attribute->name,
                                        "slug" => $bundle_product_option->attribute->slug,
                                        "link_products" => $attribute_configurable_products
                                        ->where("attribute_id", $initial_attribute_id)
                                        ->where("attribute_option_id", $initial_attribute_option_id)
                                        ->map( function ($link_product) {
                                            return [
                                                "sku" => $link_product->product->sku 
                                            ];
                                        })->toArray()
                                    ];
                                })->toArray(),
                            ];
                        })->toArray(),
                    ];
                }

                dd($configurable_products);





               $configurable_products = $attribute_configurable_products->groupBy("attribute_id")->map(function ($group_attribute) {
                    // dd($group_attribute->first()?->attribute);

                    return [
                        "attribute_id" => $group_attribute->first()?->attribute_id,
                        "name" => $group_attribute->first()?->attribute->name,
                        "slug" => $group_attribute->first()?->attribute->slug,
                        "values" => $group_attribute->map( function ($configurable_attribute) {
                            return [
                                "option_id" => $configurable_attribute->attribute_option_id,
                                "name" => $configurable_attribute->attribute_option->name,
                                "code" => $configurable_attribute->attribute_option->code,
                                "bundel_product_options" => ""
                            ];
                        })->toArray(),
                    ];
                })->toArray();
                dd($configurable_products);
                dd("asd");
                dd($attribute_configurable_products->groupBy("attribute_id"));
                
                
                
                dump($attribute_configurable_products->pluck("id"));
                dd($variant_ids);


                $product->variants->map(function ($variant) {
                    return $variant->attribute_configurable_products->map(function ($product_attr) {
                        dump($product_attr->attribute_id);
                        // dd($product_attr->);
                        dd([
                            "attribute_id" => $product_attr->attribute_id,
                            "title" => $product_attr->attribute->name,
                            "slug" => $product_attr->attribute->slug,
                            "values" => [
                                
                            ]
                        ]);

                        return;  
                    });
                });
            }

            $inventory = $product->catalog_inventories->first();
            if ($product->type == "simple") $data["stock_items"] = ["qty" =>  $inventory->quantity, "is_in_stock" => (bool) $inventory->is_in_stock ];

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
            // dd($data);
		}
		catch (Exception $exception)
		{
			throw $exception;
		}

		return $data;
	}
}