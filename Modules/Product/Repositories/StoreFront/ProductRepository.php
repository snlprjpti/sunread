<?php

namespace Modules\Product\Repositories\StoreFront;

use Exception;
// use Modules\Category\Transformers\CategoryResource;
use Modules\Category\Transformers\StoreFront\CategoryResource;
// use Modules\Category\Transformers\List\CategoryResource;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Repositories\BaseRepository;
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

			$product = Product::whereWebsiteId($website->id)->whereSku($sku)->with([ "catalog_inventories", "images", "images.types", "categories", "categories.values", "product_attributes", "product_attributes.attribute"])->firstOrFail();
			
			$data = [];
            $data["id"] = $product->id;

            $product_details = $product->product_attributes->mapWithKeys( function ($product_attribute) use ($store, $product){
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $product_attribute->attribute->id
                ];
                return (!$product_attribute->attribute->is_user_defined) ? [ $product_attribute->attribute->slug => $product->value($match) ] : [];
            })->toArray();


            $data = array_merge($data, $product_details);
            $data["categories"] = CategoryResource::collection($product->categories);

            $inventory = $product->catalog_inventories->first();
            $data["stock_items"] = ["qty" =>  $inventory->quantity, "is_in_stock" => (bool) $inventory->is_in_stock ];

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