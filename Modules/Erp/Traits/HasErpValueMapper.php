<?php

namespace Modules\Erp\Traits;

use Exception;
use Illuminate\Support\Carbon;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Erp\Entities\ErpImport;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductImage;

trait HasErpValueMapper
{
	protected array $erp_types = [
		"webAssortments",
		"listProducts",
		"attributeGroups",
		"salePrices",
		"eanCodes",
		"webInventories",
		"productDescriptions",
		"productVariants",
		"productImages"
	];

	protected array $erp_attributes = [
		"Features",
		"Size and Care",
		"Ean Code"
	];

	public function importAll(): bool
	{
		try
		{
			$erp_details = ErpImport::where("type", "listProducts")->first()->erp_import_details;
			$erp = ErpImport::all();
			$chunked = $erp_details->chunk(100); 
			foreach ( $chunked as $chunk )
			{
				foreach ( $chunk as $detail )
				{
					if ( !$detail->value["webAssortmentWeb_Active"] == true && !$detail->value["webAssortmentWeb_Setup"] == "SR") continue;
		
					$type = ($erp_details->where("sku", $detail->sku)->count() > 1) ? "configurable" : "simple";
					
					$product_data = [
						"attribute_set_id" => 1,
						"website_id" => 1,
						"sku" => $detail->sku,
						"type" => $type,
					];
		
					$match = [
						"website_id" => 1,
						"sku" => $detail->sku,
					];
		
					$product = Product::updateOrCreate($match, $product_data);
					$this->mapstoreImages($erp, $product, $detail );

					if (($erp_details->where("sku", $detail->sku)->count() > 1)) $this->createVariants($erp, $product, $detail);
					
					$this->createAttributeValue($erp, $product, $detail, false);

					$this->createInventory($erp, $product, $detail);
				}
			}
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return true;
	}

	private function createAttributeValue(mixed $erp, object $product, object $erp_product_iteration, bool $ean_code_value = true): bool
	{
		try
		{
			$ean_code = $erp->where("type", "eanCodes")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();
			$variants = $erp->where("type", "productVariants")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();

			if ( $ean_code_value )
			{
				$pluck_code = $this->getValue($variants)->first()["code"];
				$ean_code_value = $this->getValue($ean_code)->where("variantCode", $pluck_code)->first()["crossReferenceNo"] ?? "";
			}
			else{
				$ean_code_value = $this->getValue($ean_code)->first()["crossReferenceNo"] ?? "";
			}

			$description = $erp->where("type", "productDescriptions")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();
			$description = ($description->count() > 1) ? json_decode($this->getValue($description)->first(), true)["description"] ?? "" : "";
			$price = $erp->where("type", "salePrices")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();
			$price_value = ($price->count() > 1) ? $this->getValue($price)->where("currencyCode", "USD")->first() ?? ["unitPrice" => 0.0] : ["unitPrice" => 0.0];

			$attribute_data = [
				[
					"attribute_id" => 1,
					"value" => $erp_product_iteration->value["description"]
				],
				[
					"attribute_id" => 3,
					"value" => $price_value["unitPrice"], 
				],
				// [
				// 	"attribute_id" => 6,
				// 	"value" => Carbon::parse($price_value["startingDate"]), 
				// ],
				// [
				// 	"attribute_id" => 7,
				// 	"value" => Carbon::parse($price_value["endingDate"]), 
				// ],
				[
					"attribute_id" => 16,
					"value" => $description, 
				],
				[
					"attribute_id" => 17,
					"value" => \Str::limit($description, 100), 
				],
				[
					"attribute_id" => 19,
					"value" => $description, 
				],
				[
					"attribute_id" => 20,
					"value" => $erp_product_iteration->value["description"], 
				],
				[
					"attribute_id" => 21,
					"value" => $description, 
				],
				[
					"attribute_id" => 22,
					"value" => 1, 
				],
				[
					"attribute_id" => 28,
					"value" => $this->getAttributeValue($erp, $product, $erp_product_iteration ,"Features" ), 
				],
				[
					"attribute_id" => 29,
					"value" => $this->getAttributeValue($erp, $product, $erp_product_iteration ,"Size and care" ), 
				],
				[
					"attribute_id" => 30,
					"value" => $ean_code_value, 
				],
			];

			foreach ( $attribute_data as $attributeData )
			{
				$attribute = Attribute::find($attributeData["attribute_id"]);
				$attribute_type = config("attribute_types")[$attribute->type ?? "string"];
				$value = $attribute_type::updateOrCreate(["value" => $attributeData["value"]]);
				ProductAttribute::updateOrCreate([
					"attribute_id" => $attribute->id,
					"product_id"=> $product->id,
					"value_type" => $attribute_type,
					"value_id" => $value->id,
					"scope" => "website",
					"scope_id" => 1
				]);
			}

		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return true;

	}

	//This fn is for concat features and size and care values 
	private function getAttributeValue(mixed $erp, object $product, object $erp_product_iteration, string $attribute_name): string
	{
		try
		{
			$attr_groups = $erp->where("type", "attributeGroups")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get(); 

			$attach_value = "";
			if ( $attr_groups->count() > 1 )
			{
				$this->getValue($attr_groups, function ($value) use (&$attach_value, $attribute_name) {
					if ( $value["attributetype"] == $attribute_name ) $attach_value .= \Str::finish($value["description"], ". ");
				});
			}
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}
		return $attach_value;
	}

	// This fn will get value form erp detail value field 
	private function getValue(mixed $values, callable $callback = null): mixed
	{
		try
		{
			$data = $values->map( function ($value) use($callback){
				if ( $callback ) $data = $callback($value->value);
				return ( $callback ) ? $data : $value->value;
			});
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return $data;
	}

	private function mapstoreImages( mixed $erp, object $product, object $erp_product_iteration, array $variant = [] )
	{
		$image = $erp->where("type", "productImages")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();

		$images = $this->getValue($image, function ($value) {
			return json_decode($value, true);
		});

		if ( !empty($variant) ) $images = $images->where("color_code", $variant["pfVerticalComponentCode"]);

		if ( $images->count() > 0 )
		{
			$position = 0;
			foreach( $images as $image )
			{
				$position++;
				$data["path"] = $image["url"];
				$data["position"] = $position;
				$data["product_id"] = $product->id;
				
				switch ( $image["image_type"] )
				{
					case "a" :
						$data["main_image"] = 1;
					break;
		
					case "b" :
						$data["small_image"] = 1;
					break;
		
					default :
						$data["thumbnail"] = 1;
					break;
				}
				
				ProductImage::updateOrCreate($data);
			}
		}
		

	}
	
	// This fn create variants based on parent product
	private function createVariants( mixed $erp, object $product, object $erp_product_iteration )
	{
		try
		{
			$variants = $erp->where("type", "productVariants")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get(); 
			
			if ( $variants->count() > 1 )
			{
				$ean_codes = $erp->where("type", "eanCodes")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();			
				foreach ( $this->getValue($variants) as $variant )
				{
					$product_data = [
						"parent_id" => $product->id,
						"attribute_set_id" => 1,
						"website_id" => 1,
						"sku" => "{$product->sku}-variants-{$variant['code']}",
						"type" => "simple",
					];

					$match = [
						"website_id" => 1,
						"sku" => "{$product->sku}-variants-{$variant['code']}",
					];

					$variant_product = Product::updateOrCreate($match, $product_data);
					$ean_code = $this->getValue($ean_codes)->where("variantCode", $variant["code"])->first()["crossReferenceNo"] ?? "" ;
					
					$this->mapstoreImages($erp, $variant_product, $erp_product_iteration, $variant );
					
					$this->createAttributeValue($erp, $variant_product, $erp_product_iteration, $ean_code);
					$this->createInventory($erp, $variant_product, $erp_product_iteration);
				}
			}
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return true;
	}

	private function createInventory(mixed $erp, object $product, object $erp_product_iteration): bool
	{
		try
		{
			$inventory = $erp->where("type", "webInventories")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();

			if ( $inventory->count() > 1 )
			{
				$value = array_sum($this->getValue($inventory)->pluck("Inventory")->toArray());
				$data = [
					"quantity" => $value,
					"use_config_manage_stock" => 1,
					"product_id" => $product->id,
					"website_id" => $product->website_id,
					"manage_stock" =>  0,
					"is_in_stock" => (bool) ($value > 0) ? 1 : 0,
				];
		
				$match = [
					"product_id" => $product->id,
					"website_id" => $product->website_id
				];
	
				CatalogInventory::updateOrCreate($match, $data);
			}
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return true;
	}

	private function getDetailCollection(string $slug, string $sku)
	{
		# code...
	}

}